<?php
function e(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
function money(float $v): string {
  return '$' . number_format($v, 2);
}
