#!/bin/bash

echo "Checking for match statements without default cases..."
echo ""

files=$(find vendor/livewire/flux/stubs/resources/views/flux/ -name "*.blade.php" -exec grep -l "match (" {} \;)

for file in $files; do
    # Get the content around match statements
    content=$(grep -A 50 "match (" "$file")
    
    # Check if this match block has a default case
    # Extract match blocks more carefully
    if grep -q "match (" "$file"; then
        # Get line numbers with match (
        line_nums=$(grep -n "match (" "$file" | cut -d: -f1)
        
        for line_num in $line_nums; do
            # Get context starting from match line and look for closing }
            context=$(sed -n "${line_num},\$p" "$file" | head -30)
            
            # Check if default => exists in this match block
            if ! echo "$context" | grep -q "default =>"; then
                # Count how many closing braces before a default or end of statement
                echo "⚠️ POTENTIAL ISSUE: $file (line $line_num)"
                sed -n "${line_num},$((line_num+15))p" "$file" | cat -n
                echo ""
            fi
        done
    fi
done
