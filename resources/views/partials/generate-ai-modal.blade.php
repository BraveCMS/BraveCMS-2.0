<div class="modal fade" id="aiWriterModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-microchip me-1"></i> AI Article Generator
                </h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body py-2">
                <label for="aiPrompt" class="form-label">
                    Describe what the article should be about
                </label>

                <textarea id="aiPrompt" class="form-control" rows="4"
                    placeholder="Example: Write an analytical article about the impact of horses on the development of civilization."></textarea>

                <div class="form-text mt-1">
                    The AI will generate a title, short description, and full content. You must pick a category and
                    one or more tags (optional).
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn btn-sm btn-success" id="generateArticleBtn">Generate Article</button>
            </div>
        </div>
    </div>
</div>
