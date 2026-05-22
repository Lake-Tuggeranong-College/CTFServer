#!/bin/sh

# Encoded flag: LUCIDFLOW --> 12 21 3 9 4 6 12 15 23
while true; do
  {
    echo "Whisper Daemon v1.0"
    echo "--------------------"
    echo "12 21 3 9 4 6 12 15 23"
  } | nc -l -p 6500 -q 1
done
