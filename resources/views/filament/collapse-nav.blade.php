<script>
    document.addEventListener('DOMContentLoaded', () => {
        const groups = document.querySelectorAll('[data-navigation-group="Utilities"]');

        groups.forEach(group => {
            const button = group.querySelector('button');
            if (button && button.getAttribute('aria-expanded') === 'true') {
                button.click(); // collapse bij load
            }
        });
    });
</script>