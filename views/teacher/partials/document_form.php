<?php
$editingDocument = $currentDocument ?? null;
$selectedCategory = $editingDocument['category_id'] ?? '';
$selectedClass = $editingDocument['class_id'] ?? '';
$seenClasses = [];
?>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Titre</label>
        <input type="text" class="form-control" name="titre" value="<?php echo htmlspecialchars($editingDocument['titre'] ?? ''); ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Catégorie</label>
        <select class="form-select" name="category_id" required>
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['nom']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Classe</label>
        <select class="form-select" name="class_id" required>
            <option value="">-- Choisir une classe affectée --</option>
            <?php foreach ($assignments as $assignment): ?>
                <?php
                if (in_array($assignment['class_id'], $seenClasses)) {
                    continue;
                }
                $seenClasses[] = $assignment['class_id'];
                ?>
                <option value="<?php echo $assignment['class_id']; ?>" <?php echo $selectedClass == $assignment['class_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($assignment['class_name'] . ' (' . $assignment['niveau_name'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Fichier</label>
        <input type="file" class="form-control" name="document_file" <?php echo $editingDocument ? '' : 'required'; ?>>
        <div class="text-muted small mt-1">PDF, Word, PowerPoint, Excel, texte ou image. Max 10 Mo.</div>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($editingDocument['description'] ?? ''); ?></textarea>
    </div>
</div>
