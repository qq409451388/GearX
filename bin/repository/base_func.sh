#!/bin/bash

# base_func.sh

# Define colors
GREEN="\033[0;32m"
RED="\033[0;31m"
YELLOW="\033[1;33m"
BLUE="\033[0;34m"
NC="\033[0m" # No Color

# Function to print a success message in green
print_success() {
    local message=$1
    echo -e "${GREEN}✔ ${message}${NC}"
}

# Function to print an error message in red
print_error() {
    local message=$1
    echo -e "${RED}✘ ${message}${NC}"
}

# Function to print a warning message in yellow
print_warning() {
    local message=$1
    echo -e "${YELLOW}⚠ ${message}${NC}"
}

# Function to print an informational message in blue
print_info() {
    local message=$1
    echo -e "${BLUE}ℹ ${message}${NC}"
}

# Function to print a plain message without any color
print_plain() {
    local message=$1
    echo -e "${message}"
}
