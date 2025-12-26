<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Widget</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; margin: 24px; background:#0b1220; color:#e5e7eb; }
        .card { max-width: 560px; background:#111827; border:1px solid #1f2937; border-radius:16px; padding:20px; box-shadow: 0 10px 30px rgba(0,0,0,.25); }
        label { display:block; font-size:14px; margin: 12px 0 6px; color:#cbd5e1; }
        input, textarea { width:100%; box-sizing:border-box; padding:10px 12px; border-radius:10px; border:1px solid #334155; background:#0b1220; color:#e5e7eb; outline:none; }
        input:focus, textarea:focus { border-color:#60a5fa; }
        textarea { min-height: 110px; resize: vertical; }
        .row { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
        .btn { margin-top:16px; width:100%; padding:12px 14px; border-radius:12px; border:0; cursor:pointer; background:#2563eb; color:white; font-weight:600; }
        .btn:disabled { opacity:.6; cursor:not-allowed; }
        .msg { margin-top:14px; padding:12px 14px; border-radius:12px; border:1px solid; display:none; }
        .msg.success { background:#052e1a; border-color:#16a34a; color:#bbf7d0; }
        .msg.error { background:#2a0b0b; border-color:#ef4444; color:#fecaca; }
        .field-error { border-color:#ef4444 !important; }
        .errors { margin:10px 0 0; padding-left: 18px; }
        .hint { font-size:12px; color:#94a3b8; margin-top:6px; }
    </style>
</head>
<body>
<div class="card">
    <h1 style="margin:0 0 8px; font-size:18px;">Обратная связь</h1>
    <p style="margin:0 0 14px; color:#94a3b8; font-size:14px;">
        Заполните форму — мы ответим как можно скорее.
    </p>

    <div id="msgSuccess" class="msg success"></div>
    <div id="msgError" class="msg error"></div>

    <form id="ticketForm" enctype="multipart/form-data">
        <div class="row">
            <div>
                <label for="name">Имя</label>
                <input id="name" name="name" autocomplete="name" required>
            </div>
            <div>
                <label for="phone">Телефон</label>
                <input id="phone" name="phone" autocomplete="tel" placeholder="+380..." required>
            </div>
        </div>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required>

        <label for="subject">Тема</label>
        <input id="subject" name="subject" required>

        <label for="message">Сообщение</label>
        <textarea id="message" name="message" required></textarea>

        <label for="files">Файлы (опционально)</label>
        <input id="files" name="files[]" type="file" multiple>
        <div class="hint">Можна прикрепить несколько файлов.</div>

        <button id="submitBtn" class="btn" type="submit">Отправить</button>
    </form>
</div>

<script>
    const form = document.getElementById('ticketForm');
    const btn = document.getElementById('submitBtn');
    const msgSuccess = document.getElementById('msgSuccess');
    const msgError = document.getElementById('msgError');

    const fields = ['name','phone','email','subject','message','files'];

    function show(el, text) {
        el.textContent = text;
        el.style.display = 'block';
    }
    function hide(el) {
        el.textContent = '';
        el.style.display = 'none';
    }
    function clearFieldErrors() {
        fields.forEach(f => {
            const el = document.getElementById(f);
            if (el) el.classList.remove('field-error');
        });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        hide(msgSuccess);
        hide(msgError);
        clearFieldErrors();

        btn.disabled = true;
        btn.textContent = 'Отправляем...';

        try {
            const fd = new FormData(form);

            const res = await fetch('/api/tickets', {
                method: 'POST',
                body: fd,
                headers: {
                    'Accept': 'application/json',
                },
            });

            // 201/200
            if (res.ok) {
                const data = await res.json();
                const id = data?.data?.id ?? '';
                show(msgSuccess, id ? `Заявка оформлена (ID: ${id}). Мы скоро с вами свяжемся!` : 'Заявка оформлена. Мы скоро с вами свяжемся!');
                form.reset();
                return;
            }

            // rate limit
            if (res.status === 429) {
                show(msgError, 'Лимит: можна отправить не более 1 заявки в сутки.');
                return;
            }

            // validation
            if (res.status === 422) {
                const payload = await res.json();
                const errors = payload?.errors ?? {};

                // подсветить поля
                Object.keys(errors).forEach((key) => {
                    // files может прийти как "files.0" — нормализуем до "files"
                    const fieldId = key.startsWith('files') ? 'files' : key;
                    const el = document.getElementById(fieldId);
                    if (el) el.classList.add('field-error');
                });

                // список ошибок
                const lines = [];
                for (const [k, msgs] of Object.entries(errors)) {
                    (msgs || []).forEach(m => lines.push(m));
                }
                show(msgError, lines.length ? lines.join(' ') : 'Проверьте поля: есть ошибки.');
                return;
            }

            // other errors
            const text = await res.text();
            show(msgError, `Ошибка сервера (${res.status}). Попробуйте еще раз позже.`);

            console.error('Server error:', res.status, text);
        } catch (err) {
            console.error(err);
            show(msgError, 'Ошибка сети. Проверьте подключение и попробуйте еще раз.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Отправить';
        }
    });
</script>
</body>
</html>
