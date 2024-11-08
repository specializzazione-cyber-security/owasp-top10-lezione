#!/bin/bash

# URL dell'endpoint di commento
URL="http://localhost:8000/articles/1/comments"

# Max requests to sent
NUM_REQUESTS=100

# Get csrf token somehow
CSRF_TOKEN="hG9GyQB2JEtOM7UTYB4Jj4igadFIemXtGJzXBg3y"

# Retrieve the session coockie somehow (csrf attack)
SESSION_COOKIE="laravel_session=eyJpdiI6ImRISWJFcktLSHJkcG9MY3ZoZU1WV0E9PSIsInZhbHVlIjoidEszdjJBbWNQNUFhaWsvMC85Ti8yTzQ2WENXRXh0cTFIODByWXdzSEVxaXYwOC9oVFcyOFM4TUhkWGxDQnkydUh5Nm52RHlLWkVyTjN1eGV3ZGJDYWdpMUd5aVhtZy9GRTF2cENWcXl4aE5tVThRT0tZZnQ2UEswL2FOL3pmNkwiLCJtYWMiOiJmN2UzYzc3N2UyNWZjNTFjZjRkMzI4MTIyZDM2MWU0NTc4OWFkM2E3ZTgxZTljMTM2ZGU1NjBjNDBkMmIxMzc0IiwidGFnIjoiIn0%3D"

# Send a comment including csrf token
send_comment() {
    local comment_number=$1
    curl -s -H "Cookie: $SESSION_COOKIE" -X POST -d "content=Questo è un commento di prova $1&_token=$CSRF_TOKEN" "$URL"
}

# Sending multiple comments
for ((i = 1; i <= NUM_REQUESTS; i++))
do
    send_comment $i
    echo "Comment $i sent"
done
