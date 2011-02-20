<?php
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Say>Please state your event after the beep, and remember to speak clearly.</Say>
    <Record transcribe="true" transcribeCallback="<?php 
        echo "addEvent.php?phone=".urlencode($_REQUEST['Caller']); ?>"        
        action="goodbye.php" maxLength="120" />
</Response>