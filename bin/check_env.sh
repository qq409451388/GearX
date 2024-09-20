#!/bin/bash

# 引入库文件
source ./base_func.sh
source ./language.sh

# 输出脚本版本
print_info "$VERSION_MSG"

# 检查 PHP 是否安装
if ! command -v php &> /dev/null; then
    print_error "$PHP_NOT_INSTALLED_MSG"
    exit 1
fi

# 获取 PHP 版本
PHP_VERSION=$(php -r "echo PHP_VERSION;")
if [[ $(echo "$PHP_VERSION 7.4" | awk '{print ($1 > $2)}') -eq 1 ]]; then
    print_warning "$PHP_VERSION_WARNING_MSG"
fi
if [[ $(echo "$PHP_VERSION 8.1" | awk '{print ($1 > $2)}') -eq 1 ]]; then
    print_success "$PHP_VERSION_SUCCESS_MSG"
fi

# 检查 PHP 必须组件
MISSING_COMPONENTS=()
for component in curl pdo mysqli openssl sockets json pdo_mysql libxml mbstring; do
    if ! php -m | grep -q "$component"; then
        MISSING_COMPONENTS+=("$component")
    fi
done

if [ ${#MISSING_COMPONENTS[@]} -eq 0 ]; then
    print_success "$PHP_COMPONENTS_SUCCESS_MSG"
else
    print_error "$PHP_COMPONENTS_ERROR_MSG ${MISSING_COMPONENTS[*]}"
fi

# 检测操作系统
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$NAME
else
    OS=$(uname -s)
fi
print_info "Detected OS: $OS"

# 针对不同系统的处理（如果需要）
case "$OS" in
    *Ubuntu*|*Debian*)
        print_info "Detected Ubuntu/Debian based system"
        # 在这里添加特定于 Ubuntu/Debian 的逻辑
        ;;
    *CentOS*|*Red\ Hat*|*Fedora*)
        print_info "Detected CentOS/Red Hat/Fedora based system"
        # 在这里添加特定于 CentOS/Red Hat 的逻辑
        ;;
    *)
        print_warning "Unsupported or unrecognized operating system"
        ;;
esac
