{{-- Shares a copy of a CMS document to the Shared Library, from the CMS or from the editor. --}}
<style>
    .library-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1000; align-items: center; justify-content: center; font-family: Arial, sans-serif; }
    .library-overlay.open { display: flex; }
    .library-box { background: #fff; border-radius: 10px; padding: 22px 24px; width: 460px; max-width: 95vw; max-height: 92vh; overflow-y: auto; box-shadow: 0 8px 32px rgba(0,0,0,0.25); color: #333; text-align: left; }
    .library-heading { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
    .library-note { font-size: 11.5px; color: #777; line-height: 1.5; margin-bottom: 14px; }
    .library-field { margin-bottom: 12px; }
    .library-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
    .library-field input, .library-field select, .library-field textarea { width: 100%; box-sizing: border-box; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; font-family: Arial, sans-serif; outline: none; background: #fff; }
    .library-field input:focus, .library-field select:focus, .library-field textarea:focus { border-color: #0f2557; }
    .library-field textarea { resize: vertical; min-height: 56px; }
    .library-message { display: none; font-size: 12px; line-height: 1.5; padding: 9px 11px; border-radius: 6px; margin-bottom: 12px; white-space: pre-line; }
    .library-message.error { display: block; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; }
    .library-message.success { display: block; background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; }
    .library-actions { display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 16px; }
    .library-actions a, .library-actions button { font-size: 12.5px; font-weight: 600; padding: 8px 16px; border-radius: 5px; cursor: pointer; text-decoration: none; font-family: Arial, sans-serif; border: 1px solid #ccc; background: #fff; color: #444; }
    .library-actions .library-primary { background: #0f2557; border-color: #0f2557; color: #fff; }
    .library-actions button:disabled { opacity: 0.6; cursor: wait; }
</style>

<div class="library-overlay" id="library-overlay" role="dialog" aria-labelledby="library-heading">
    <form class="library-box" id="library-form" onsubmit="submitLibraryShare(event)">
        <div class="library-heading" id="library-heading">Share to Library</div>
        <div class="library-note" id="library-note"></div>
        <div class="library-message" id="library-message"></div>
        <div id="library-fields">
            <div class="library-field">
                <label for="library-course">Share to <span style="color:#ef4444">*</span></label>
                <select id="library-course" name="course_id">
                    <option value="">Whole program ({{ auth()->user()->program }})</option>
                    @foreach($libraryCourses as $course)
                        <option value="{{ $course->id }}">{{ $course->code }} — {{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="library-field">
                <label for="library-type">Category <span style="color:#ef4444">*</span></label>
                <select id="library-type" name="type" required>
                    @foreach(\App\Models\SharedResource::typeOptions() as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="library-field">
                <label for="library-title">Title <span style="color:#ef4444">*</span></label>
                <input type="text" id="library-title" name="title" maxlength="255" required>
            </div>
            <div class="library-field">
                <label for="library-description">Description</label>
                <textarea id="library-description" name="description" maxlength="500" placeholder="What faculty can use this for..."></textarea>
            </div>
        </div>
        <div class="library-actions">
            <a class="library-primary" id="library-go" href="{{ url('/faculty/shared-library') }}" target="_blank" style="display:none;">Open Shared Library</a>
            <button type="button" id="library-close" onclick="closeLibraryShare()">Cancel</button>
            <button type="submit" class="library-primary" id="library-submit">Share</button>
        </div>
    </form>
</div>

<script>
    var libraryShareUrl = null;
    var libraryShared = false;

    function libraryMessage(kind, text) {
        var box = document.getElementById('library-message');
        box.className = 'library-message' + (kind ? ' ' + kind : '');
        box.textContent = text || '';
    }

    function openLibraryShare(button) {
        var entry = button.dataset.entry ? JSON.parse(button.dataset.entry) : null;
        var type = document.getElementById('library-type');
        libraryShareUrl = button.dataset.url;
        libraryShared = false;
        libraryMessage();
        document.getElementById('library-fields').style.display = '';
        document.getElementById('library-submit').style.display = '';
        document.getElementById('library-go').style.display = 'none';
        document.getElementById('library-close').textContent = 'Cancel';
        document.getElementById('library-submit').textContent = entry ? 'Update in Library' : 'Share';
        document.getElementById('library-note').textContent = entry
            ? 'This document is already in the Shared Library. Your latest version will replace the shared copy.'
            : 'Faculty in your program can view and download a copy of your latest version. Your own document stays private and only you can edit it.';
        document.getElementById('library-title').value = entry ? entry.title : button.dataset.title;
        document.getElementById('library-description').value = entry ? (entry.description || '') : '';
        document.getElementById('library-course').value = entry && entry.course_id ? String(entry.course_id) : '';
        if (entry) type.value = entry.type;
        document.getElementById('library-overlay').classList.add('open');
    }

    function closeLibraryShare() {
        document.getElementById('library-overlay').classList.remove('open');
        // The CMS card now shows the document as shared.
        if (libraryShared && document.querySelector('[data-library-refresh]')) window.location.reload();
    }

    async function libraryRequest(path, method, body) {
        var response = await fetch(libraryShareUrl + path, {
            method: method,
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
            body: body ? JSON.stringify(body) : undefined,
        });
        var result = await response.json().catch(function () { return {}; });
        if (!response.ok) {
            var errors = result.errors ? Object.values(result.errors).flat() : [];
            throw new Error(errors[0] || result.message || 'The document could not be shared. Please try again.');
        }
        return result;
    }

    // An open editor holds changes it hasn't saved to AcadLink yet, so ask it to save and
    // wait for the file to arrive before copying it.
    async function waitForLibrarySave() {
        var save = await libraryRequest('/prepare', 'POST');
        if (!save.saving) return;
        for (var i = 0; i < 20; i++) {
            await new Promise(function (resolve) { setTimeout(resolve, 1000); });
            if ((await libraryRequest('/status', 'GET')).stamp !== save.stamp) return;
        }
        throw new Error('Your latest changes are still saving. Please try again in a moment.');
    }

    async function submitLibraryShare(event) {
        event.preventDefault();
        var submit = document.getElementById('library-submit');
        var label = submit.textContent;
        submit.disabled = true;
        libraryMessage();
        try {
            submit.textContent = 'Saving your changes…';
            await waitForLibrarySave();
            submit.textContent = 'Sharing…';
            var form = new FormData(document.getElementById('library-form'));
            var result = await libraryRequest('', 'POST', Object.fromEntries(form.entries()));
            libraryShared = true;
            libraryMessage('success', result.message);
            document.getElementById('library-fields').style.display = 'none';
            submit.style.display = 'none';
            document.getElementById('library-go').style.display = '';
            document.getElementById('library-close').textContent = 'Close';
        } catch (e) {
            libraryMessage('error', e.message);
        } finally {
            submit.disabled = false;
            submit.textContent = label;
        }
    }
</script>
