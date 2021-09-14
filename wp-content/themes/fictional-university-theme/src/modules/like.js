(function($) {
    class Like {
        constructor() {
            this.events();
            console.log('Like JS running.');
        }

        // Events
        events() {
            $('.like-box').on('click', this.clickDispatcher.bind(this));
        }

        // Methods
        clickDispatcher(event) {
            let currentLikeBox = $(event.target).closest('.like-box');

            if (currentLikeBox.attr('data-exists') == 'yes') {
                this.deleteLike(currentLikeBox);
            } else {
                this.createLike(currentLikeBox);
            }
        }

        createLike(currentLikeBox) {
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
                url: universityData.root_url + '/wp-json/university/v1/manage-like',
                type: 'POST',
                data: {
                    professorId: currentLikeBox.data('professor')
                },
                success: (response) => {
                    currentLikeBox.attr('data-exists', 'yes');
                    let likeCount = parseInt(currentLikeBox.find('.like-count').html(), 10);
                    likeCount += 1;
                    currentLikeBox.find('.like-count').html(likeCount);
                    currentLikeBox.attr('data-like', response);
                    console.log(response);
                },
                error: (response) => {
                    console.log(response.responseText);
                }
            });
        }

        deleteLike(currentLikeBox) {
            $.ajax({
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
                },
                url: universityData.root_url + '/wp-json/university/v1/manage-like',
                data: {
                    like: currentLikeBox.attr('data-like')
                },
                type: 'DELETE',
                success: (response) => {
                    currentLikeBox.attr('data-exists', 'no');
                    let likeCount = parseInt(currentLikeBox.find('.like-count').html(), 10);
                    likeCount -= 1;
                    currentLikeBox.find('.like-count').html(likeCount);
                    currentLikeBox.attr('data-like', '');
                    console.log(response);
                },
                error: (response) => {
                    console.log(response);
                }
            });
        }
    }

    const like = new Like();
})(jQuery);