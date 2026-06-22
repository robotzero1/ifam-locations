jQuery(document).ready(function($) {
    $('.ifam-locations-sortable tbody').sortable({
        handle: '.drag-handle',
        cursor: 'move',
        axis: 'y',
        update: function(event, ui) {
            var order = [];
            $(this).find('tr').each(function() {
                order.push($(this).data('id'));
            });

            $.ajax({
                url: ifamLocations.restUrl + 'reorder',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ order: order }),
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', ifamLocations.nonce);
                },
                success: function(response) {
                    if (response.success) {
                        $('.ifam-locations-sortable tbody tr').each(function(index) {
                            $(this).find('td:nth-child(8)').text(index + 1);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Reorder failed:', xhr.responseJSON);
                }
            });
        }
    });
});