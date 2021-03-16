<script type="text/javascript">
    phpVars = new Array();
    <?php foreach($vars as $var) {
        echo 'phpVars.push("' . $var . '");';
        echo 'test';
    };
    ?>
</script>
