You're part of a secret underground syndicate trying to take over the government and just intercepted a mesage, you must decrypt the message so we can figure out their plan

P =61
Q =7
A = 17
B = 11


You are trying to find a and b such that A=Q^a mod P and B=Q^b mod P
.
Iterate Through Possible Values:

For 
a, test values from 1 to P−1 to see which one satisfies 7^a mod 61=A.
For b, test values from 1 to P−1 to see which one satisfies 7^b mod 61=B.
Discrete Logarithm Problem: This is an example of the discrete logarithm problem. The difficulty of solving this problem is what makes Diffie-Hellman secure when large prime numbers are used. However, since 

Find the Shared Secret: Once you find a and b, you can compute the shared secret S either as S=B^a mod P or S=A^b mod P
.
Because the modulus 61 is small, this method is computationally feasible; for larger values, it becomes impractical.


Message = W@RoFmuzKgawgi
