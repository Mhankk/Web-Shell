<script type="text/javascript">
    document.write('hieki');
    phpVars = new Array();
    <?php foreach($vars as $var) {
        echo 'phpVars.push("' . $var . '");';
        echo 'test';
    };
    ?>
</script>
