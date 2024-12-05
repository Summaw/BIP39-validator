# BIP39 Seed Phrase Validator

A secure web tool for validating Bitcoin BIP39 seed phrases. This tool allows users to verify if their seed phrase follows the BIP39 standard specification without exposing their private keys.

## Features

- Validates 12, 15, 18, 21, and 24-word BIP39 seed phrases
- Checks for:
  - Correct number of words
  - Valid BIP39 wordlist words
  - Duplicate words
  - Valid checksum
  - Proper phrase structure
- Clean, user-friendly interface
- Client-side input handling
- Secure server-side validation
- Requires PHP with GMP extension

## Security

- Runs completely locally - no external API calls
- No seed phrase storage
- No private key generation
- Input sanitization and validation

## Dependencies

- BitWasp/bitcoin-php library
- PHP 7.0+ with GMP extension

# Valid BIP39 generator:
- https://iancoleman.io/bip39/
