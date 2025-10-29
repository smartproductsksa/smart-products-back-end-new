#!/bin/bash

# Generate Self-Signed SSL Certificate for Development
# For production, replace with real SSL certificates from Let's Encrypt or other CA

echo "Generating self-signed SSL certificate..."

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout key.pem \
    -out cert.pem \
    -subj "/C=US/ST=State/L=City/O=Organization/OU=IT/CN=localhost"

echo "SSL certificates generated successfully!"
echo "  - Certificate: cert.pem"
echo "  - Private Key: key.pem"
echo ""
echo "Note: These are self-signed certificates for DEVELOPMENT only."
echo "For PRODUCTION, use Let's Encrypt or purchase certificates from a CA."


