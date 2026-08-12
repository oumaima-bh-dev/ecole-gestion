<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Appel & Présences</h1>
    </div>

    <!-- Filters card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Sélectionnez la classe et la date</h5>
            <form action="index.php" method="GET" class="row g-3">
                <input type="hidden" name="controller" value="teacher">
                <input type="hidden" name="action" value="attendance">
                
                <div class="col-md-5">
                    <select class="form-select" name="class_id" required>
                        <option value="">-- Choisir une classe --</option>
                        <?php 
                        // To avoid duplicates of classes in the select menu (since teacher can be assigned to multiple subjects in same class)
                        $seenClasses = [];
                        foreach ($assignments as $asg): 
                            if (in_array($asg['class_id'], $seenClasses)) continue;
                            $seenClasses[] = $asg['class_id'];
                        ?>
                            <option value="<?php echo $asg['class_id']; ?>" <?php echo ($selectedClassId == $asg['class_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($asg['class_name'] . ' (' . $asg['niveau_name'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($dateAbsence); ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance table -->
    <?php if ($isValidClass): ?>
        <?php
        $clsName = '';
        foreach ($assignments as $asg) {
            if ($asg['class_id'] == $selectedClassId) {
                $clsName = $asg['class_name'];
                break;
            }
        }
        ?>
        <form action="index.php?controller=teacher&action=attendance&class_id=<?php echo $selectedClassId; ?>&date=<?php echo urlencode($dateAbsence); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="custom-table-container">
                <h5 class="fw-bold mb-4"><i class="fa-solid fa-clipboard-user text-primary me-2"></i>Feuille de présence : <strong><?php echo htmlspecialchars($clsName); ?></strong> - Date : <strong><?php echo date('d/m/Y', strtotime($dateAbsence)); ?></strong></h5>

                <div class="table-responsive mb-4">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr class="table-light">
                                <th class="ps-4 text-uppercase text-secondary small tracking-wider">Élève</th>
                                <th class="text-center text-uppercase text-secondary small tracking-wider" style="width: 140px;">Présence</th>
                                <th class="text-uppercase text-secondary small tracking-wider" style="width: 120px;">Justifié</th>
                                <th class="pe-4 text-uppercase text-secondary small tracking-wider">Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <?php 
                                // Default to present if no attendance recorded yet
                                $status = isset($student['statut']) ? $student['statut'] : 'present';
                                $just = isset($student['justifie']) ? $student['justifie'] : 0;
                                $motif = isset($student['motif']) ? $student['motif'] : '';
                                ?>
                                <tr>
                                    <td class="ps-4 align-middle">
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?></div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            <input type="radio" class="btn-check" name="attendance[<?php echo $student['student_id']; ?>]" id="pres_<?php echo $student['student_id']; ?>" value="present" <?php echo ($status === 'present') ? 'checked' : ''; ?> onchange="toggleAbsenceFields(<?php echo $student['student_id']; ?>, false)">
                                            <label class="btn btn-outline-success px-3" for="pres_<?php echo $student['student_id']; ?>" title="Présent"><i class="fa-solid fa-check"></i></label>

                                            <input type="radio" class="btn-check" name="attendance[<?php echo $student['student_id']; ?>]" id="abs_<?php echo $student['student_id']; ?>" value="absent" <?php echo ($status === 'absent') ? 'checked' : ''; ?> onchange="toggleAbsenceFields(<?php echo $student['student_id']; ?>, true)">
                                            <label class="btn btn-outline-danger px-3" for="abs_<?php echo $student['student_id']; ?>" title="Absent"><i class="fa-solid fa-xmark"></i></label>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm" name="justified[<?php echo $student['student_id']; ?>]" id="just_<?php echo $student['student_id']; ?>" <?php echo ($status === 'present') ? 'disabled' : ''; ?> style="border-radius: 8px;">
                                            <option value="0" <?php echo ($just == 0) ? 'selected' : ''; ?>>Non</option>
                                            <option value="1" <?php echo ($just == 1) ? 'selected' : ''; ?>>Oui</option>
                                        </select>
                                    </td>
                                    <td class="pe-4 align-middle">
                                        <input type="text" class="form-control form-control-sm" name="motifs[<?php echo $student['student_id']; ?>]" id="motif_<?php echo $student['student_id']; ?>" value="<?php echo htmlspecialchars($motif); ?>" placeholder="Saisir un motif..." <?php echo ($status === 'present') ? 'disabled' : ''; ?> style="border-radius: 8px;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Sauvegarder la feuille d'appel
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function toggleAbsenceFields(studentId, isAbsent) {
    const justSelect = document.getElementById('just_' + studentId);
    const motifInput = document.getElementById('motif_' + studentId);
    if (justSelect && motifInput) {
        justSelect.disabled = !isAbsent;
        motifInput.disabled = !isAbsent;
        if (!isAbsent) {
            justSelect.value = "0";
            motifInput.value = "";
        }
    }
}
</script>
