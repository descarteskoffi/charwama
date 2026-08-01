<?php
/**
 * Layout administration - Pied de page (Admin Footer)
 */
?>
            </main>
        </div>
    </div>

    <!-- Modal Popup de confirmation de suppression -->
    <div id="adminConfirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--bg-card); width:90%; max-width:420px; padding:32px; text-align:center; border-radius:16px; border-left:4px solid #ef4444; box-shadow:0 20px 60px rgba(0,0,0,0.6); position:relative;">
            <div style="font-size:3rem; color:#ef4444; margin-bottom:16px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 style="margin-bottom:12px; font-family:var(--font-titles); font-size:1.2rem;">Confirmation de suppression</h3>
            <p id="adminConfirmMessage" style="color:var(--text-muted); font-size:0.95rem; margin-bottom:28px; line-height:1.6;">
                Êtes-vous sûr de vouloir supprimer cet élément ?
            </p>
            <div style="display:flex; gap:12px;">
                <button type="button" id="adminConfirmCancelBtn"
                    style="flex:1; padding:12px; background:var(--bg-input); color:var(--text-body); border:1px solid var(--border-color); border-radius:var(--radius-md); font-family:var(--font-titles); font-weight:600; cursor:pointer; font-size:0.95rem;">
                    Annuler
                </button>
                <button type="button" id="adminConfirmSubmitBtn"
                    style="flex:1; padding:12px; background:#ef4444; color:#fff; border:none; border-radius:var(--radius-md); font-family:var(--font-titles); font-weight:700; cursor:pointer; font-size:0.95rem;">
                    <i class="fa-solid fa-trash-can"></i> Supprimer
                </button>
            </div>
        </div>
    </div>

    <script>
        let deleteUrlTarget = null;

        function confirmDeletion(event, itemName) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Récupérer le lien parent A s'il existe (que l'on ait cliqué sur l'icône i ou le a)
            let target = event.currentTarget || event.target;
            while (target && target.tagName !== 'A') {
                target = target.parentElement;
            }

            if (!target || !target.href) {
                console.error("Impossible de récupérer l'URL de suppression.");
                return false;
            }

            deleteUrlTarget = target.href;

            const modal = document.getElementById('adminConfirmModal');
            const message = document.getElementById('adminConfirmMessage');

            message.innerHTML = 'Êtes-vous sûr de vouloir supprimer <strong>' + itemName + '</strong> ?'
                + '<br><span style="font-size:0.8rem; color:#ef4444; display:block; margin-top:6px;">Cette action est irréversible.</span>';

            modal.style.display = 'flex';
            return false;
        }

        document.getElementById('adminConfirmCancelBtn').addEventListener('click', function() {
            document.getElementById('adminConfirmModal').style.display = 'none';
            deleteUrlTarget = null;
        });

        document.getElementById('adminConfirmSubmitBtn').addEventListener('click', function() {
            if (deleteUrlTarget) {
                window.location.href = deleteUrlTarget;
            }
        });

        document.getElementById('adminConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                deleteUrlTarget = null;
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('adminConfirmModal').style.display = 'none';
                deleteUrlTarget = null;
            }
        });
    </script>
</body>
</html>
