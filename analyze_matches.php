<?php
$directory = 'vendor/livewire/flux/stubs/resources/views/flux/';

$files = [];
$it = new RecursiveDirectoryIterator($directory);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ($file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.php') !== false) {
        $files[] = $file->getPathname();
    }
}

sort($files);

echo "=== ANALYZING MATCH STATEMENTS ===\n\n";

$issues_found = [];

foreach ($files as $filepath) {
    $content = file_get_contents($filepath);
    
    // Find all match( patterns
    if (preg_match_all('/match\s*\(\s*\$([^\)]+)\s*\)\s*\{([^}]*\{[^}]*\})*[^}]*\}/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        
        // For each match, check the full block
        preg_match_all('/match\s*\(\s*\$([^\)]+)\s*\)\s*\{/', $content, $start_matches, PREG_OFFSET_CAPTURE);
        
        foreach ($start_matches[0] as $idx => $match) {
            $start_pos = $match[1];
            $var_name = $start_matches[1][$idx][0];
            
            // Find the closing brace for this match block
            $brace_count = 0;
            $pos = $start_pos + strlen($match[0]) - 1;
            $in_match = false;
            
            while ($pos < strlen($content)) {
                if ($content[$pos] === '{') {
                    $brace_count++;
                    $in_match = true;
                } elseif ($content[$pos] === '}' && $in_match) {
                    $brace_count--;
                    if ($brace_count === 0) {
                        break;
                    }
                }
                $pos++;
            }
            
            $match_block = substr($content, $start_pos, $pos - $start_pos + 1);
            
            // Check if this match block has a default case
            if (strpos($match_block, 'default =>') === false) {
                $line_num = substr_count($content, "\n", 0, $start_pos) + 1;
                $issues_found[$filepath][] = [
                    'line' => $line_num,
                    'var' => $var_name,
                    'block' => $match_block
                ];
            }
        }
    }
}

if (empty($issues_found)) {
    echo "✅ ALL MATCH STATEMENTS HAVE DEFAULT CASES!\n";
} else {
    echo "⚠️ ISSUES FOUND: " . count($issues_found) . " files with potentially missing defaults\n\n";
    
    foreach ($issues_found as $file => $matches) {
        echo "FILE: $file\n";
        foreach ($matches as $match) {
            echo "  Line {$match['line']}: match (\${$match['var']})\n";
            echo "  Block preview:\n";
            $lines = explode("\n", $match['block']);
            foreach (array_slice($lines, 0, 3) as $line) {
                echo "    " . trim($line) . "\n";
            }
            echo "\n";
        }
    }
}
?>
