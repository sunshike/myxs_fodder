<?php
$sql = "

DROP TABLE `ims_myxs_fodder_class`, `ims_myxs_fodder_content`, `ims_myxs_fodder_system`, `ims_myxs_fodder_day_sign`, `ims_myxs_fodder_member`, `ims_myxs_fodder_operation_log`, `ims_myxs_fodder_grouping`, , `ims_myxs_fodder_advert`, `ims_myxs_fodder_admin_intergral_log`, `ims_myxs_fodder_grouping_bg`, `ims_myxs_fodder_member_intergral_log`, `ims_myxs_fodder_shuffling`;

";
pdo_run($sql);
?>