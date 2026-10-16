# The Anonymous Host

To solve this challenge, you will need to identify the IP addresses that are found in the log file, and then determine which of those addresses are private or reserved for local networks.

## Hints

### Hint 1: 

Research RFC 1918 to see which IPv4 ranges are reserved strictly for private local networks.

### Hint 2: 

127.0.0.0/8 is reserved for loopback (localhost), whilst 169.254.0.0/16 is used for APIPA (link-local) addresses.