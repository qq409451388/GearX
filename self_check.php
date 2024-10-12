<?php
function checkDependencies($directory, $rules) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());

            // Match class definition to get the current class name
            preg_match('/\bclass\s+([a-zA-Z0-9_]+)/', $content, $classMatch);
            $currentClass = $classMatch[1] ?? null;

            // Match different types of dependencies
            preg_match_all('/\bnew\s+([a-zA-Z0-9_\\\\]+)|([a-zA-Z0-9_\\\\]+)::|extends\s+([a-zA-Z0-9_\\\\]+)|implements\s+([a-zA-Z0-9_\\\\, ]+)|\binstanceof\s+([a-zA-Z0-9_\\\\]+)|function\s+[a-zA-Z0-9_]+\s*\([^)]*\)\s*:\s*([a-zA-Z0-9_\\\\]+)|\buse\s+([a-zA-Z0-9_\\\\]+);/', $content, $matches);

            // Combine all matches into a single list
            $dependencies = array_merge(
                $matches[1], // new ClassName
                $matches[2], // ClassName::method
                $matches[3], // extends ClassName
                explode(',', implode(',', $matches[4])), // implements Interface1, Interface2
                $matches[5], // instanceof ClassName
                $matches[6], // function return type
                $matches[7]  // use TraitName
            );

            $dependencies = array_filter(array_map('trim', $dependencies)); // Remove empty matches and trim spaces

            foreach ($dependencies as $dependency) {
                // Allow self, static, parent, and current class
                if (in_array($dependency, ['self', 'static', 'parent','void','bool','string','int', $currentClass])) {
                    continue;
                }

                $isValid = false;
                $dependency2 = findFileByClassName($directory, $dependency);
                foreach ($rules as $rule) {
                    //var_dump($file, $dependency, $rule, $dependency2);
                    $matchres = preg_match($rule, $dependency2);
                    if ($matchres) {
                        $isValid = true;
                        break;
                    }
                }
                if (!$isValid) {
                    echo "Invalid dependency in file {$file->getPathname()}: {$dependency}\n";
                }
            }
        }
    }
}

function findFileByClassName($directory, $className) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());

            // Match class definition to get the current class name
            if (preg_match('/\bclass\s+' . preg_quote($className, '/') . '\b/', $content)) {
                return $file->getPathname();
            }
        }
    }
    return null;
}

// Define dependency rules for each package
$rules = [
    'core/native' => [
        // Allow dependencies within native package
        '/^core[\\/\\\\]native[\\/\\\\]/',
    ],
   /* 'core/annotation' => [
        // Can only depend on core classes
        '/^core[\\/\\\\]/',
    ],
    'core/generics' => [
        // Can depend on native classes
        '/^core[\\/\\\\]native[\\/\\\\]/',
    ],*/
];

// Check each package
foreach ($rules as $package => $rule) {
    echo "Checking $package...\n";
    checkDependencies($package, $rule);
}
