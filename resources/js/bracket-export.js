import { toPng } from 'html-to-image';

window.exportBracket = function (filename) {
    const btn = document.getElementById('export-btn');
    const label = document.getElementById('export-label');
    const content = document.getElementById('bracket-content');
    const container = document.getElementById('bracket-container');

    if (!content) return;

    label.textContent = 'Memproses...';
    if (btn) btn.disabled = true;

    // Temporarily remove overflow constraints so the full bracket is captured
    const origContentOverflow = content.style.overflow;
    const origContainerOverflow = container ? container.style.overflow : '';

    content.style.overflow = 'visible';
    if (container) container.style.overflow = 'visible';

    // Use the outer element (inline-flex) which has the full natural width
    const outer = document.getElementById('bracket-outer');
    const target = outer || content;

    // Force a brief reflow so the browser calculates the full dimensions
    void target.offsetHeight;

    toPng(target, {
        backgroundColor: '#ffffff',
        pixelRatio: 2,
        cacheBust: true,
        width: target.scrollWidth,
        height: target.scrollHeight,
        canvasWidth: target.scrollWidth * 2,
        canvasHeight: target.scrollHeight * 2,
    })
        .then((dataUrl) => {
            const link = document.createElement('a');
            link.download = filename || 'bracket.png';
            link.href = dataUrl;
            link.click();
        })
        .catch((err) => {
            alert('Gagal export gambar: ' + err.message);
        })
        .finally(() => {
            // Restore original overflow
            content.style.overflow = origContentOverflow;
            if (container) container.style.overflow = origContainerOverflow;

            label.textContent = 'Export Gambar';
            if (btn) btn.disabled = false;
        });
};
