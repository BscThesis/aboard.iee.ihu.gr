<?php
// INITIALIZE REACT BASED ON public/index.html builded file
$index_data = file_get_contents(base_path('public/index.html'));

echo $index_data;
?>