<?php

/**
 * php ./init_dependency.php -i /Users/xxx/.ssh/github_rsa -r true -m ssh
 */
$gearPath = dirname(__FILE__, 2);
$moduleInfo = json_decode(file_get_contents($gearPath . "/config/module_dependency.data"), true);
$args = [];
for ($i = 1;$i < $argc; $i+=2) {
    $args[$argv[$i]] = $argv[$i + 1];
}

$rsa = empty($args["-i"]) ? null : $args["-i"];
if (!empty($rsa) && !file_exists($rsa)) {
    exit("设置了一个不存在的私钥:".$rsa);
}
$fetchModel = empty($args["-m"]) ? "https" : $args["-m"];
$moduleList = empty($args["-l"]) ? null : $args["-l"];
if (!is_null($moduleList)) {
    $moduleList = explode(",", $moduleList);
    $repositoryNew = [];
    foreach ($moduleInfo['repository'] as $repository) {
        if (in_array($repository['alias'], $moduleList)) {
            $repositoryNew[] = $repository;
        }
    }
    $moduleInfo['repository'] = $repositoryNew;
}

$runRightNow = !empty($args["-r"]) && $args["-r"] === 'true';

$modulePath =  $gearPath.DIRECTORY_SEPARATOR.'modules';
if (!is_dir($modulePath)) {
    mkdir($modulePath);
}

$target = "/tmp/module_dependency.tmp";
switch ($fetchModel) {
    case "ssh":
        $result = fetchWithSsh($moduleInfo);
        break;
    case "https":
        $result = fetchWithHttps($moduleInfo);
        break;
    default:
        exit("未知拉取方式，使用默认值HTTPS");
}

if ($runRightNow) {
    exec("ssh-add $rsa", $output, $return);
    foreach ($result as $alias => $command) {
        $command = $command." ".$modulePath.DIRECTORY_SEPARATOR.$alias;
        echo "[EXEC]".$command.PHP_EOL;
        exec($command);
    }
} else {
    file_put_contents($target, json_encode($result, JSON_UNESCAPED_SLASHES));
}

function fetchWithHttps($moduleInfo) {
    $result = [];
    $repository = $moduleInfo["repository"];
    foreach ($repository as $item) {
        $moduleName = $item['name'];
        $cmd = "git clone https://{$moduleInfo['server']}/{$moduleInfo['at']}/{$moduleName}.git";
        $result[$item['alias']] = $cmd;
    }

    return $result;
}

function fetchWithSsh($moduleInfo) {
    $result = [];
    $repository = $moduleInfo["repository"];
    foreach ($repository as $item) {
        $moduleName = $item['name'];
        $cmd = "git clone git@{$moduleInfo['server']}:{$moduleInfo['at']}/{$moduleName}.git";
        $result[$item['alias']] = $cmd;
    }

    return $result;
}
