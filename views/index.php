<?php
use watrlabs\watrkit\pagebuilder;
$pagebuilder = new pagebuilder();
$pagebuilder->set_page_name("Landing");
$pagebuilder->buildheader();
?>

<h1><?=$_ENV["APP_NAME"]?></h1>

</body>
</html>
