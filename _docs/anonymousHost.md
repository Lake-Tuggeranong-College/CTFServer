# The anonymous Host

#### Hint 1
Not every IP address that appears in a connection log belongs to a device on the public internet. Look for differences in the addresses.

#### Hint 2
Review the IPv4 ranges reserved for private networks, loopback addresses, and link-local communication. These are not ordinary public internet addresses.

#### Hints 3
Compare each source IP against these ranges: 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16, 127.0.0.0/8, and 169.254.0.0/16. The remaining address is the one to investigate. Remember that documentation-reserved IP ranges are also not publicly routable.
