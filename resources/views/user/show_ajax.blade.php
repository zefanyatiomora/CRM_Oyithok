<div id="modal-user" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail User</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID User</th> 
                    <td>{{ $user->user_id }}</td> 
                </tr> 
                <tr> 
                    <th>Username</th> 
                    <td>{{ $user->username }}</td> 
                </tr> 
                <tr> 
                    <th>Nama</th> 
                    <td>{{ $user->nama }}</td> 
                </tr> 
                <tr> 
                    <th>Level</th> 
                    <td>{{ $user->level->level_nama }}</td> 
                </tr> 
                <!-- Add more fields if necessary -->
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#modal-user').on('show.bs.modal', function(event) {
            let userId = $(event.relatedTarget).data('id'); // Assuming user ID is passed via data attribute

            $.ajax({
                url: '/user/show_ajax/' + userId,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        // Populate modal fields with the retrieved data
                        $('#user_id').text(response.data.user_id);
                        $('#username').text(response.data.username);
                        $('#nama').text(response.data.nama);
                        $('#level').text(response.data.level ? response.data.level.level_nama : 'N/A'); // Assuming level relationship is loaded
                        $('#avatar').html('<img src="' + response.data.avatar + '" alt="Avatar" style="width:50px;height:50px;"/>');
                        // Populate other fields as necessary
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to fetch user data.'
                    });
                }
            });
        });
    });
</script>
    