{{-- JavaScript for common.site.booking-form (needs jQuery) --}}
<script>
$(function () {
    var allSlots = @json(\App\Models\Booking::TIME_SLOTS);
    var currency = @json(config('gardener.currency_symbol'));
    var t = {
        select: @json(__('Select a Time Slot')),
        loading: @json(__('Loading slots...')),
        none: @json(__('No free slots on this day — pick another date')),
        error: @json(__('Error loading slots')),
        base: @json(__('Base price')),
        perSize: @json(__('per 100 sq ft')),
        final: @json(__('Final price is confirmed after our visit.'))
    };

    $('.booking-form').each(function () {
        var form = $(this);

        // "For myself" fills in my details; "someone else" clears them
        form.find('.booking-for').on('change', function () {
            var mine = $(this).val() === 'myself';
            ['.bf-name', '.bf-email', '.bf-phone'].forEach(function (sel) {
                var input = form.find(sel);
                input.val(mine ? input.data('mine') : '');
            });
            if (!mine) form.find('.bf-name').focus();
        });

        // Free time slots for the chosen date
        var slot = form.find('.bf-slot');
        var lastRequest = 0;
        form.find('.bf-date').on('change', function () {
            var date = $(this).val();
            if (!date) return;
            var requestId = ++lastRequest;
            // Keep the slot the visitor already picked (or the one from a failed submit)
            var keep = slot.val() || slot.data('old');
            slot.html('<option value="">' + t.loading + '</option>').prop('disabled', true).css('color', '#6c757d');

            $.ajax({
                url: @json(route('checkSlots')),
                data: { date: date },
                dataType: 'json',
                success: function (fullSlots) {
                    if (requestId !== lastRequest) return; // an older answer arrived late — ignore it
                    var free = allSlots.filter(function (s) { return !fullSlots.includes(s); });
                    var html = '<option value="">' + (free.length ? t.select : t.none) + '</option>';
                    free.forEach(function (s) {
                        html += '<option value="' + s + '"' + (s === keep ? ' selected' : '') + '>' + s + '</option>';
                    });
                    slot.html(html).prop('disabled', false).css('color', '#212529');
                },
                error: function () {
                    if (requestId !== lastRequest) return;
                    slot.html('<option value="">' + t.error + '</option>').prop('disabled', false);
                }
            });
        });
        if (form.find('.bf-date').val()) form.find('.bf-date').trigger('change');

        // Live price estimate = base price + (size / 100 × price per 100 sq ft)
        function estimate() {
            var opt = form.find('.bf-service option:selected');
            var base = parseFloat(opt.data('price')) || 0;
            var per100 = parseFloat(opt.data('per100')) || 0;
            var size = parseInt(form.find('.bf-size').val(), 10) || 0;
            var box = form.find('.bf-estimate-box');

            if (!opt.val() || (!base && !per100)) { box.hide(); return; }

            var total = base + (size && per100 ? size / 100 * per100 : 0);
            form.find('.bf-estimate').text(currency + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            var note = per100
                ? t.base + ' ' + currency + base + ' + ' + currency + per100 + ' ' + t.perSize + '. '
                : '';
            form.find('.bf-estimate-note').text(note + t.final);
            box.show();
        }
        form.find('.bf-service, .bf-size').on('change input', estimate);
        estimate();
    });
});
</script>
