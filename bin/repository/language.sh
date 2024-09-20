#!/bin/bash

# 检测系统语言
LANGUAGE=$(echo $LANG | cut -d '_' -f 1)

# 根据语言环境定义文本
if [ "$LANGUAGE" = "zh" ]; then
    VERSION_MSG="脚本版本: 0.0.1 beta"
    PHP_NOT_INSTALLED_MSG="PHP 未安装"
    PHP_VERSION_WARNING_MSG="PHP 版本高于 7.4，注意兼容性"
    PHP_VERSION_SUCCESS_MSG="PHP 版本高于 8.1，版本兼容性良好"
    PHP_COMPONENTS_SUCCESS_MSG="所有必需的 PHP 组件已安装"
    PHP_COMPONENTS_ERROR_MSG="缺少必需的 PHP 组件: "

    # 新增消息
    MSG_CHOOSE_INSTALL_METHOD="选择安装方式:"
    MSG_INVALID_OPTION="无效的选项。"
    MSG_HTTPS_INSTALL_COMPLETE="HTTPS 模式安装完成。"
    MSG_SSH_INSTALL_COMPLETE="SSH 模式安装完成。"
    MSG_CLONE_EXAMPLE="拉取示例项目代码到"
    MSG_EXAMPLE_CLONE_COMPLETE="示例项目代码拉取完成。"
    MSG_START_SERVICE="是否要启动服务? (y (default)/n):"
    MSG_SERVICE_STARTED="服务已启动。"
    MSG_SERVICE_NOT_STARTED="服务未启动。"
    MSG_CHECK_ENV="是否需要检查环境? (y (default)/n):"
    MSG_ENV_CHECK_COMPLETE="环境检查已完成。"
    MSG_SKIP_ENV_CHECK="跳过环境检查。"
    MSG_ENTER_INSTALL_PATH="请输入安装路径 (默认: /home/release):"
    MSG_ENTER_INSTALL_OPTION="请输入选项 (1 或 2):"
    MSG_USE_CUSTOM_SSH="是否要指定本地证书文件路径? (y/n (default)):"
    MSG_ENTER_SSH_PATH="请输入 SSH 密钥路径 (默认: $SSH_KEY_PATH):"
    MSG_INSTALL_DIR_EXISTS="安装目录下已经存在 GearXExample 文件夹，脚本终止。"
    MSG_CREATE_INSTALL_DIR_FAILED="无法创建安装目录，脚本终止。"
    MSG_INSTALL_DIR_CREATED="安装目录创建成功："
else
    VERSION_MSG="Script version: 0.0.1 beta"
    PHP_NOT_INSTALLED_MSG="PHP is not installed"
    PHP_VERSION_WARNING_MSG="PHP version is greater than 7.4, check compatibility"
    PHP_VERSION_SUCCESS_MSG="PHP version is greater than 8.1, good compatibility"
    PHP_COMPONENTS_SUCCESS_MSG="All required PHP components are installed"
    PHP_COMPONENTS_ERROR_MSG="Missing required PHP components: "

    # 新增消息
    MSG_CHOOSE_INSTALL_METHOD="Choose installation method:"
    MSG_INVALID_OPTION="Invalid option."
    MSG_HTTPS_INSTALL_COMPLETE="HTTPS installation completed."
    MSG_SSH_INSTALL_COMPLETE="SSH installation completed."
    MSG_CLONE_EXAMPLE="Cloning example project to"
    MSG_EXAMPLE_CLONE_COMPLETE="Example project clone completed."
    MSG_START_SERVICE="Do you want to start the service? (y (default)/n):"
    MSG_SERVICE_STARTED="Service has been started."
    MSG_SERVICE_NOT_STARTED="Service not started."
    MSG_CHECK_ENV="Do you want to check the environment? (y (default)/n):"
    MSG_ENV_CHECK_COMPLETE="Environment check completed."
    MSG_SKIP_ENV_CHECK="Skipping environment check."
    MSG_ENTER_INSTALL_PATH="Enter installation path (default: /home/release):"
    MSG_ENTER_INSTALL_OPTION="Enter option (1 (default) or 2):"
    MSG_USE_CUSTOM_SSH="Do you want to specify a custom SSH key path? (y/n(default)):"
    MSG_ENTER_SSH_PATH="Enter SSH key path (default: $SSH_KEY_PATH):"
    MSG_INSTALL_DIR_EXISTS="Installation directory already contains GearXExample folder, aborting script."
    MSG_CREATE_INSTALL_DIR_FAILED="Failed to create installation directory, aborting script."
    MSG_INSTALL_DIR_CREATED="Installation directory created successfully: "
fi
