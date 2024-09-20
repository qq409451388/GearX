#!/bin/bash

# language.sh

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
else
    VERSION_MSG="Script version: 0.0.1 beta"
    PHP_NOT_INSTALLED_MSG="PHP is not installed"
    PHP_VERSION_WARNING_MSG="PHP version is greater than 7.4, check compatibility"
    PHP_VERSION_SUCCESS_MSG="PHP version is greater than 8.1, good compatibility"
    PHP_COMPONENTS_SUCCESS_MSG="All required PHP components are installed"
    PHP_COMPONENTS_ERROR_MSG="Missing required PHP components: "
fi
