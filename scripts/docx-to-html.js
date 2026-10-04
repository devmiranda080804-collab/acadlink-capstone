import mammoth from 'mammoth';

const filePath = process.argv[2];

if (!filePath) {
    process.stderr.write('Usage: node scripts/docx-to-html.js <path-to-docx>\n');
    process.exit(1);
}

try {
    const result = await mammoth.convertToHtml({ path: filePath }, {
        styleMap: [
            'u => u',
            'strike => s',
        ],
    });

    process.stdout.write(result.value);
} catch (err) {
    process.stderr.write(String(err && err.message ? err.message : err) + '\n');
    process.exit(1);
}
