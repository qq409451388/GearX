#!/bin/bash

# 获取当前脚本所在目录
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# 引入库文件
source "$SCRIPT_DIR/repository/base_func.sh"
source "$SCRIPT_DIR/repository/language.sh"

# 输出脚本版本信息
print_info "$VERSION_MSG"

# 询问用户是否需要检查环境
read -p "$MSG_CHECK_ENV" check_env_choice
if [[ "$check_env_choice" == "y" ]]; then
    source "$SCRIPT_DIR/check_env.sh"
    print_success "$MSG_ENV_CHECK_COMPLETE"
else
    print_info "$MSG_SKIP_ENV_CHECK"
fi

# 询问用户输入安装路径
read -p "$MSG_ENTER_INSTALL_PATH" install_path
install_path="${install_path:-/home/release}"

# 去掉路径后面的斜线
install_path="${install_path%/}"

# 定义目录路径
GEARX_PATH="$install_path/GearX"
EXAMPLE_PATH="$install_path/GearXExample"
SSH_KEY_PATH="$HOME/.ssh/github_rsa"

# 检查并创建安装目录
if [[ -d "$GEARX_PATH" || -d "$EXAMPLE_PATH" ]]; then
    print_error "$MSG_INSTALL_DIR_EXISTS"
    exit 1
fi

if [[ ! -d "$install_path" ]]; then
    mkdir -p "$install_path"
    if [[ $? -ne 0 ]]; then
        print_error "$MSG_CREATE_INSTALL_DIR_FAILED"
        exit 1
    fi
    print_success "$MSG_INSTALL_DIR_CREATED $install_path"
fi

# 3.2. 安装 Module
print_info "$MSG_CHOOSE_INSTALL_METHOD"
echo "1) 使用 HTTPS"
echo "2) 使用 SSH"
read -p "$MSG_ENTER_INSTALL_OPTION" install_choice

if [[ "$install_choice" == "1" ]]; then
    # 3.2.1 使用 HTTPS 的方式安装
    php "$GEARX_PATH/bin/init_dependency.php" -r true -m https
    print_success "$MSG_HTTPS_INSTALL_COMPLETE"
elif [[ "$install_choice" == "2" ]]; then
    # 3.2.2 使用 SSH 的方式安装
    read -p "$MSG_USE_CUSTOM_SSH" use_custom_ssh_key
    if [[ "$use_custom_ssh_key" == "y" ]]; then
        read -p "$MSG_ENTER_SSH_PATH" custom_ssh_key
        SSH_KEY_PATH="${custom_ssh_key:-$SSH_KEY_PATH}"
    fi
    php "$GEARX_PATH/bin/init_dependency.php" -r true -m ssh -i "$SSH_KEY_PATH"
    print_success "$MSG_SSH_INSTALL_COMPLETE"
else
    print_error "$MSG_INVALID_OPTION"
    exit 1
fi

# 3.3. 将示例项目代码拉取到用户指定的目录
print_info "$MSG_CLONE_EXAMPLE $EXAMPLE_PATH"
cd "$install_path" && git clone https://github.com/qq409451388/GearXExample
print_success "$MSG_EXAMPLE_CLONE_COMPLETE"

# 3.4 启动服务
read -p "$MSG_START_SERVICE" start_service
if [[ "$start_service" == "y" ]]; then
    php "$EXAMPLE_PATH/scripts/http_server.php" -PappPath="$EXAMPLE_PATH" -PgearPath="$GEARX_PATH" -PconfigPath="$EXAMPLE_PATH/config"
    print_success "$MSG_SERVICE_STARTED"
else
    print_warning "$MSG_SERVICE_NOT_STARTED"
fi
