<?php defined('BASEPATH') OR exit('No direct script access allowed');

function ticket_number($sequence) { return 'PDE/' . date('Ymd') . '/' . str_pad($sequence, 4, '0', STR_PAD_LEFT); }
function ticket_code($prefix, $id) { return $prefix . '-' . date('Ymd') . '-' . str_pad($id, 6, '0', STR_PAD_LEFT); }
function status_badge($status, $color = '#8C8C8C') { return '<span class="status-badge" style="--status-color:' . html_escape($color) . '">' . html_escape($status) . '</span>'; }
