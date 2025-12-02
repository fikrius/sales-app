<?php

namespace App\Helpers;

class Helper
{
    /**
     * Generate a unique code with prefix and date
     *
     * @param string $prefix The prefix for the code (e.g., 'SALE', 'ITM')
     * @param string $modelClass The full model class name
     * @param string $column The column name to check for existing codes (default: 'code')
     * @return string Generated code in format: PREFIX-YYYYMMDD-XXXX
     */
    public static function generateCode($prefix, $modelClass, $column = 'code')
    {
        $today = date('Ymd');
        $pattern = $prefix . '-' . $today . '-%';
        
        // Get table name from model
        $table = (new $modelClass)->getTable();
        
        // Use raw query with FOR UPDATE to lock the rows
        $lastCode = \DB::selectOne(
            "SELECT $column FROM $table WHERE $column LIKE ? ORDER BY $column DESC LIMIT 1 FOR UPDATE",
            [$pattern]
        );
        
        if ($lastCode) {
            // Extract the number from the last code (e.g., SL-20251202-0016 -> 16)
            $parts = explode('-', $lastCode->$column);
            $lastNumber = isset($parts[2]) ? (int)$parts[2] : 0;
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        // Generate the code
        $code = sprintf('%s-%s-%04d', $prefix, $today, $number);
        
        return $code;
    }
    
    /**
     * Format currency to Indonesian Rupiah
     *
     * @param float|int $amount The amount to format
     * @return string Formatted currency string
     */
    public static function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
    
    /**
     * Format date to Indonesian format
     *
     * @param string $date The date string
     * @param string $format The output format (default: 'd/m/Y H:i')
     * @return string Formatted date string
     */
    public static function formatDate($date, $format = 'd/m/Y H:i:s')
    {
        return date($format, strtotime($date));
    }
}
