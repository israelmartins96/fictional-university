(function($) {
    class MyNotes {
        constructor() {
            this.events();
            let self = this;
        }
        
        events() {
            $('#my-notes').on('click', '.delete-note', this.deleteNote.bind(this));
            $('#my-notes').on('click', '.edit-note', this.editNote.bind(this));
            $('#my-notes').on('click', '.update-note', this.updateNote.bind(this));
            $('.submit-note').on('click', this.createNote.bind(this));
        }

        // Methods
        // Edit Note
        editNote(event) {
            let thisNote = $(event.target).parents('li');
            if (thisNote.data('state') == 'editable') {
                // Make note read only
                this.makeNoteReadOnly(thisNote);
            } else {
                // Make note editable
                this.makeNoteEditable(thisNote);
            }
        }

        makeNoteEditable(thisNote) {
            thisNote.find('.edit-note').html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
            thisNote.find('.note-title-field, .note-body-field').removeAttr('readonly').addClass('note-active-field');
            thisNote.find('.update-note').addClass('update-note--visible');
            thisNote.data('state', 'editable');
        }

        makeNoteReadOnly(thisNote) {
            thisNote.find('.edit-note').html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
            thisNote.find('.note-title-field, .note-body-field').attr('readonly', 'readonly').removeClass('note-active-field');
            thisNote.find('.update-note').removeClass('update-note--visible');
            thisNote.data('state', 'readonly');
        }

        // Create Note
        createNote() {
            let newPost = {
                'title': $('.new-note-title').val(),
                'content': $('.new-note-body').val(),
                'status': 'publish'
            };
            
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
                url: universityData.root_url + '/wp-json/wp/v2/note/',
                type: 'POST',
                data: newPost,
                success: (response) => {
                    $('.new-note-title, .new-note-body').val('');
                    $(`
                    <li data-id="${response.id}">
                        <input readonly class="note-title-field" value="${response.title.raw}">
                        <button class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                        <button class="delete-note"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                        <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                        <button class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</button>
                    </li>
                    `).prependTo('#my-notes').hide().slideDown();
                    console.log('New note request sent.');
                    console.log(response);
                },
                error: (response) => {
                    if (response.responseText == 'You have reached your note limit.') {
                        $('.note-limit-message').addClass('active');
                    }

                    console.log('Sorry, new note request not sent.');
                    console.log(response);
                }
            });
        }

        // Update Note
        updateNote(event) {
            let thisNote = $(event.target).parents('li');

            let updatedPost = {
                'title': thisNote.find('.note-title-field').val(),
                'content': thisNote.find('.note-body-field').val()
            };
            
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
                url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
                type: 'POST',
                data: updatedPost,
                success: (response) => {
                    this.makeNoteReadOnly(thisNote);
                    console.log('Update request sent.');
                    console.log(response);
                },
                error: (response) => {
                    console.log('Sorry, update request not sent.');
                    console.log(response);
                }
            });
        }

        // Delete Note
        deleteNote(event) {
            let thisNote = $(event.target).parents('li');
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
                url: universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
                type: 'DELETE',
                success: (response) => {
                    thisNote.slideUp();
                    console.log('Delete request sent.');
                    console.log(response);

                    if (response.userNoteCount < 1) {
                        $('.note-limit-message').removeClass('active');
                    }
                },
                error: (response) => {
                    console.log('Sorry, delete request not sent.');
                    console.log(response);
                }
            });
        }
    }

    myNotes = new MyNotes();
})(jQuery);