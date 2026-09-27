#!/usr/bin/env bash
set -u

if [[ $# -lt 2 || $# -gt 3 ]]; then
    printf 'Usage: %s PRESIGNED_URL FILE [CONTENT_TYPE]\n' "$0" >&2
    exit 2
fi

url=$1
file=$2
content_type=${3:-}

if [[ ! -f $file ]]; then
    printf 'File not found: %s\n' "$file" >&2
    exit 2
fi

actual_size=$(wc -c < "$file")
content_type_args=()
if [[ -n $content_type ]]; then
    content_type_args+=(--header "Content-Type: $content_type")
fi
printf 'Testing correct Content-Length (%s bytes)...\n' "$actual_size"
correct_status=$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' \
    --max-time 20 --request PUT "${content_type_args[@]}" --upload-file "$file" "$url")
printf 'Correct length HTTP status: %s\n' "$correct_status"

wrong_size=$((actual_size + 1))
printf 'Testing incorrect Content-Length (%s bytes declared, %s sent)...\n' "$wrong_size" "$actual_size"
wrong_status=$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' \
    --max-time 20 --request PUT "${content_type_args[@]}" --header "Content-Length: $wrong_size" --upload-file "$file" "$url")
printf 'Incorrect length HTTP status: %s\n' "$wrong_status"

if [[ $correct_status != 2?? || $wrong_status == 2?? ]]; then
    printf 'Unexpected result: expected correct=2xx and incorrect=non-2xx.\n' >&2
    exit 1
fi

printf 'Content-Length signature check passed.\n'
