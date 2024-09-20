#!/bin/bash

# 获取当前脚本所在目录
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# 引入库文件
source "$SCRIPT_DIR/repository/base_func.sh"
source "$SCRIPT_DIR/repository/language.sh"

# 输出脚本版本
print_info "$VERSION_MSG"

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

# 检查 PHP 是否安装
if ! command -v php &> /dev/null; then
    print_error "$PHP_NOT_INSTALLED_MSG"
    exit 1
fi

# 获取 PHP 版本并提取主要版本号
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR_VERSION=$(echo "$PHP_VERSION" | cut -d '-' -f 1)

# 使用 sort -V 进行版本比较
if [[ "$(printf '%s\n' "8.1" "$PHP_MAJOR_VERSION" | sort -V | head -n1)" == "8.1" && "$PHP_MAJOR_VERSION" != "8.1" ]]; then
    print_success "$PHP_VERSION_SUCCESS_MSG"
elif [[ "$(printf '%s\n' "7.4" "$PHP_MAJOR_VERSION" | sort -V | head -n1)" == "7.4" && "$PHP_MAJOR_VERSION" != "7.4" ]]; then
    print_warning "$PHP_VERSION_WARNING_MSG"
fi

# 检查 PHP 必须组件
for component in curl pdo mysqli openssl sockets json pdo_mysql libxml mbstring; do
    if php -m | grep -q "$component"; then
        print_success "Component $component is installed."
    else
        print_error "Component $component is missing."
    fi
done
