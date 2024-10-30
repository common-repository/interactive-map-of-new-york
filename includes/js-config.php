<script type="text/javascript">
    var nyus_map_org_config = {
        'default': {
            'nyusbrdrclr_org': '<?php echo sanitize_hex_color($this->options['nyusbrdrclr_org']) ?>',
        },
    <?php foreach ($this->options['prepared_list'] as $item) { ?>
    'nyusmapvorg_<?php echo esc_attr($item['index']) ?>': {
            'url': '<?php echo sanitize_url($item['url']) ?>',
            'targt': '<?php echo esc_attr($item['turl']) ?>',
            'upclr': '<?php echo sanitize_hex_color($item['upclr']) ?>',
            'ovrclr': '<?php echo sanitize_hex_color($item['ovrclr']) ?>',
            'dwnclr': '<?php echo sanitize_hex_color($item['dwnclr']) ?>',
            'enbl': <?php echo esc_attr($item['enbl']) ? 'true' : 'false' ?>,
        },
    <?php } ?>
}
</script>
