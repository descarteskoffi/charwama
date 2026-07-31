<?php
/**
 * Modèle Schedule - Gestion des horaires d'ouverture et fermetures temporaires
 */
class Schedule extends Model {

    /**
     * Récupère les horaires de la semaine
     */
    public function getWeeklySchedules() {
        $stmt = $this->db->query("SELECT * FROM horaires_restaurant ORDER BY jour_semaine ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Met à jour les horaires d'un jour de la semaine
     */
    public function updateDaySchedule($jour_semaine, $ouvert, $heure_ouverture, $heure_fermeture) {
        $stmt = $this->db->query("
            UPDATE horaires_restaurant 
            SET ouvert = :ouvert, heure_ouverture = :heure_ouverture, heure_fermeture = :heure_fermeture 
            WHERE jour_semaine = :jour_semaine
        ");
        return $stmt->execute([
            ':jour_semaine' => $jour_semaine,
            ':ouvert' => (int)$ouvert,
            ':heure_ouverture' => $heure_ouverture,
            ':heure_fermeture' => $heure_fermeture
        ]);
    }

    /**
     * Détermine si le restaurant est actuellement ouvert
     * Retourne un tableau avec 'isOpen' (bool) et 'message' (string)
     */
    public function checkStatus() {
        // Charger les paramètres généraux
        require_once APPROOT . '/models/Setting.php';
        $settingModel = new Setting();
        $settings = $settingModel->getAll();

        // 1. Vérification de la fermeture manuelle immédiate
        $etatManuel = $settings['etat_ouverture_manuel'] ?? 'ouvert';
        if ($etatManuel === 'ferme') {
            return [
                'isOpen' => false,
                'reason' => 'manual',
                'message' => 'Le restaurant est actuellement fermé temporairement sur décision de la direction.'
            ];
        }

        $now = new DateTime('now', new DateTimeZone('Africa/Porto-Novo')); // Fuseau horaire du Bénin par défaut
        $currentDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        $currentTimestamp = $now->getTimestamp();

        // 2. Vérification d'une fermeture exceptionnelle à date précise
        $excDate = $settings['fermeture_exceptionnelle_date'] ?? '';
        if (!empty($excDate) && $excDate === $currentDate) {
            return [
                'isOpen' => false,
                'reason' => 'exceptional',
                'message' => 'Le restaurant est fermé exceptionnellement pour la journée du ' . date('d/m/Y', strtotime($excDate)) . '.'
            ];
        }

        // 3. Vérification d'une fermeture temporaire (période de vacances/travaux)
        $tempStart = $settings['fermeture_temporaire_debut'] ?? '';
        $tempEnd = $settings['fermeture_temporaire_fin'] ?? '';
        if (!empty($tempStart) && !empty($tempEnd)) {
            $tsStart = strtotime($tempStart);
            $tsEnd = strtotime($tempEnd);
            if ($currentTimestamp >= $tsStart && $currentTimestamp <= $tsEnd) {
                return [
                    'isOpen' => false,
                    'reason' => 'temporary',
                    'message' => 'Le restaurant est fermé temporairement du ' . date('d/m/Y H:i', $tsStart) . ' au ' . date('d/m/Y H:i', $tsEnd) . '.'
                ];
            }
        }

        // 4. Vérification de l'horaire de la semaine régulier
        $dayOfWeek = (int)$now->format('w'); // 0 (dimanche) à 6 (samedi)
        
        $stmt = $this->db->query("SELECT * FROM horaires_restaurant WHERE jour_semaine = :jour");
        $stmt->execute([':jour' => $dayOfWeek]);
        $schedule = $stmt->fetch();

        if (!$schedule || $schedule['ouvert'] == 0) {
            return [
                'isOpen' => false,
                'reason' => 'weekly_closed',
                'message' => 'Le restaurant est fermé aujourd\'hui.'
            ];
        }

        $openTime = $schedule['heure_ouverture'];
        $closeTime = $schedule['heure_fermeture'];

        // Cas spécial : horaires de nuit (ex: de 18:00 à 02:00)
        if ($closeTime < $openTime) {
            // Si l'heure courante est après l'ouverture ou avant la fermeture
            $isBetween = ($currentTime >= $openTime || $currentTime <= $closeTime);
        } else {
            $isBetween = ($currentTime >= $openTime && $currentTime <= $closeTime);
        }

        if (!$isBetween) {
            $formattedOpen = date('H\hi', strtotime($openTime));
            return [
                'isOpen' => false,
                'reason' => 'hours',
                'message' => 'Le restaurant est actuellement fermé. Les commandes reprendront à ' . $formattedOpen . '.'
            ];
        }

        return [
            'isOpen' => true,
            'message' => 'Le restaurant est ouvert.'
        ];
    }
}
