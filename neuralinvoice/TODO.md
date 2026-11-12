# TODO
- Investigate why `duration` is showing as `Array` in `client-invoice-list.php`.
- Fix PHP/SQL query to return scalar `duration_days` for each invoice (avoid arrays due to missing select fields or grouping collisions).
- Update `client-invoice-list.php` rendering to handle both string/array cases safely.
- Validate by loading `client-invoice-list.php` and checking the Duration column.

