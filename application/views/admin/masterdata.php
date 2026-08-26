<style>
    .admin-layout { display: block; }
    .admin-nav { padding: 10px; }
    .admin-nav a { display: block; padding: 11px 12px; border-radius: 8px; color: #536274; font-weight: 600; text-decoration: none; }
    .admin-nav a.active, .admin-nav a:hover { background: #eaf0ff; color: #246bfd; }
    .master-toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
    .master-toolbar p { margin: 4px 0 0; color: #738092; }
    .admin-actions { display: flex; gap: 7px; }
    .danger { color: #c43d3d; background: #fff0f0; }
    .status-dot { display: inline-block; width: 10px; height: 10px; margin-right: 6px; border-radius: 50%; }
    .modal { position: fixed; z-index: 20; inset: 0; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(16, 30, 48, .42); }
    .modal.open { display: flex; }
    .modal-card { width: min(620px, 100%); max-height: 90vh; padding: 24px; overflow: auto; border-radius: 16px; background: #fff; box-shadow: 0 20px 60px rgba(16, 30, 48, .24); }
    .modal-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .modal-head button { border: 0; background: transparent; font-size: 24px; cursor: pointer; }
    @media (max-width: 800px) { .admin-layout { grid-template-columns: 1fr; } .admin-nav { display: flex; gap: 4px; overflow: auto; } .admin-nav a { white-space: nowrap; } }
</style>

<div class="admin-layout">
    <section class="panel">
        <div class="master-toolbar">
            <div>
                <h2><?= html_escape($meta['title']) ?></h2>
                <p>Kelola data yang digunakan oleh sistem ticketing.</p>
            </div>
            <button class="button primary" type="button" onclick="openForm()">+ Tambah data</button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <?php foreach ($meta['fields'] as $label): ?>
                            <th><?= html_escape($label) ?></th>
                        <?php endforeach; ?>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$rows): ?>
                        <tr><td class="empty" colspan="<?= count($meta['fields']) + 1 ?>">Belum ada data.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($meta['fields'] as $field => $label): ?>
                                <td>
                                    <?php if ($section === 'users' && $field === 'is_active'): ?>
                                        <?= $row->$field ? 'Aktif' : 'Nonaktif' ?>
                                    <?php elseif ($section === 'users' && $field === 'role_id'): ?>
                                        <?= html_escape($row->nama_role) ?>
                                    <?php elseif ($section === 'users' && $field === 'unit_id'): ?>
                                        <?= html_escape($row->nama_unit ?: '—') ?>
                                    <?php elseif ($section === 'statuses' && $field === 'color_code'): ?>
                                        <span class="status-dot" style="background:<?= html_escape($row->$field) ?>"></span><?= html_escape($row->$field) ?>
                                    <?php else: ?>
                                        <?= html_escape($row->$field) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <div class="admin-actions">
                                    <button class="button small ghost" type="button" onclick='openForm(<?= html_escape(json_encode($row), TRUE) ?>)'>Edit</button>
                                    <form method="post" action="<?= site_url('admin/masterdata/delete/'.$section.'/'.$row->id) ?>" onsubmit="return confirm('Hapus data ini?')">
                                        <button class="button small danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal" id="master-modal">
    <form class="modal-card" method="post" id="master-form">
        <div class="modal-head">
            <div>
                <h2 id="form-title">Tambah <?= html_escape($meta['title']) ?></h2>
                <p class="muted">Kolom bertanda * wajib diisi.</p>
            </div>
            <button type="button" onclick="closeForm()">×</button>
        </div>

        <div class="form-grid">
            <?php foreach ($meta['fields'] as $field => $label): ?>
                <label>
                    <?= html_escape($label) ?>
                    <?php if ($section === 'users' && $field === 'is_active'): ?>
                        <select name="<?= $field ?>" id="field-<?= $field ?>"><option value="1">Aktif</option><option value="0">Nonaktif</option></select>
                    <?php elseif ($section === 'users' && $field === 'role_id'): ?>
                        <select name="<?= $field ?>" id="field-<?= $field ?>" required><option value="">Pilih role</option><?php foreach ($roles as $role): ?><option value="<?= $role->id ?>"><?= html_escape($role->nama_role) ?></option><?php endforeach; ?></select>
                    <?php elseif ($section === 'users' && $field === 'unit_id'): ?>
                        <select name="<?= $field ?>" id="field-<?= $field ?>"><option value="">Tanpa unit</option><?php foreach ($units as $unit): ?><option value="<?= $unit->id ?>"><?= html_escape($unit->nama_unit) ?></option><?php endforeach; ?></select>
                    <?php else: ?>
                        <input id="field-<?= $field ?>" name="<?= $field ?>" type="<?= $field === 'email' ? 'email' : ($field === 'stok' || $field === 'urutan' ? 'number' : ($field === 'color_code' ? 'color' : 'text')) ?>" <?= in_array($field, array('satuan', 'no_hp', 'color_code')) ? '' : 'required' ?>>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>

            <?php if ($section === 'users'): ?>
                <label>Password<input id="field-password" name="password" type="password" placeholder="Wajib untuk pengguna baru"></label>
            <?php endif; ?>
        </div>

        <div class="form-actions"><button class="button ghost" type="button" onclick="closeForm()">Batal</button><button class="button primary">Simpan</button></div>
    </form>
</div>

<script>
    const modal = document.getElementById('master-modal');
    const form = document.getElementById('master-form');
    const section = <?= json_encode($section) ?>;
    const baseSaveUrl = <?= json_encode(site_url('admin/masterdata/save/')) ?>;
    const formTitle = <?= json_encode($meta['title']) ?>;

    function closeForm() { modal.classList.remove('open'); }

    function openForm(row) {
        form.action = baseSaveUrl + section + (row ? '/' + row.id : '');
        document.getElementById('form-title').textContent = (row ? 'Edit ' : 'Tambah ') + formTitle;
        form.reset();

        if (row) {
            Object.keys(row).forEach(function(key) {
                const field = document.getElementById('field-' + key);
                if (field) field.value = row[key] ?? '';
            });
            const password = document.getElementById('field-password');
            if (password) password.placeholder = 'Kosongkan jika tidak diubah';
        }
        modal.classList.add('open');
    }

    modal.addEventListener('click', function(event) { if (event.target === modal) closeForm(); });
</script>
