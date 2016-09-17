function sendSMS(number,message) {
        $.ajax({
                type: "POST",
                url: "http://textbelt.com/text",
                data: "&number="+number+"&message="+message,
                success: function(info) {
                },
                error: function(info) {
                        // Or log error if POST fails.
                        console.log('Ajax POST failed:'+JSON.stringify(info));
                }
        });
}
