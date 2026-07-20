<?php
/**
 * Layout administration - Pied de page (Admin Footer)
 */
?>
            </main>
        </div>
    </div>

    <!-- Script de confirmation global pour suppression -->
    <script>
        function confirmDeletion(event, itemName) {
            if (!confirm("Êtes-vous sûr de vouloir supprimer " + itemName + " ? Cette action est irréversible.")) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
