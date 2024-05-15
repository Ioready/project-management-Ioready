<script src="{{ asset('js/jquery.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function closeScript() {
        setTimeout(function () {
            window.open(window.location, '_self').close();
        }, 120000); // 2 minutes = 120000 milliseconds
    }
    // 1 minutes = 60000 milliseconds
    
    $(window).on('load', function () {
        var element = document.getElementById('boxes');
        var opt = {
            filename: '{{Utility::customerInvoiceNumberFormat($invoice->invoice_id)}}',
            image: {type: 'jpeg', quality: 1},
            html2canvas: {scale: 4, dpi: 72, letterRendering: true},
            jsPDF: {unit: 'in', format: 'A4'}
        };
        html2pdf().set(opt).from(element).save().then(closeScript);
    });

</script>
