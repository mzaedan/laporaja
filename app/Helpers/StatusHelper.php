<?php

if (!function_exists('getStatusBadgeClass')) {
    /**
     * Get the appropriate badge class for a given status
     *
     * @param string|null $status
     * @return string
     */
    function getStatusBadgeClass($status = null)
    {
        $status = strtolower($status ?? '');
        
        return match($status) {
            'draft' => 'bg-secondary',
            'diproses' => 'bg-info',
            'dalam penanganan' => 'bg-primary',
            'selesai' => 'bg-success',
            'ditolak' => 'bg-danger',
            'tertunda' => 'bg-warning',
            default => 'bg-secondary',
        };
    }
}
