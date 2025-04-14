<div class="modal fade" id="deleteCollectingLineModal" tabindex="-1" role="dialog" aria-labelledby="deleteCollectingLineModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCollectingLineModalLabel">{{__('Delete Collecting Line')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Are you sure you want to delete this collecting line?')}}</p>
                <form id="deleteCollectingLineForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_id" name="id">
                    <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>