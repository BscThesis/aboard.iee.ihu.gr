<template>
    <div class="block">
        <!-- Title -->
        <page-title title="API Documentation"></page-title>

        <!-- Announcements -->
        <div class="block">
            <!-- Docs List -->
            <div class="block">
                <h3 class="subtitle is-3 has-text-centered">Announcements</h3>
            </div>

            <doc-item
                title="/api/announcements?users[]=userId1&amp;users[]=userId2&amp;tags[]=tagId1&amp;tags[]=tagId2&amp;perPage=5&amp;sortId=0&amp;q=%22words+words%22"
                requestType="GET"
                description="/api/announcements - Retrieves all annnouncements paginated by 10 and sorted with pinned first without any params.                
                 /api/announcements?params - Retrieves filtered announcements with params. Param example can be seen in the title.
                 Every param is optional and doesn't require to be added."
                :response="announcementsResponse"
            ></doc-item>

            <doc-item
                title="/api/announcements/{id}"
                requestType="GET"
                description="Retrieves annnouncement info with the id of {id}"
                :response="singleAnnouncementResponse"
            ></doc-item>

            <doc-item
                title="/api/announcements"
                requestType="POST"
                description="Creates a new annnouncement"
                :request="announcementCreateRequest"
                :response="announcementCreateResponse"
                auth
            ></doc-item>

            <doc-item
                title="/api/announcements/{id}"
                requestType="PUT"
                description="Updates annnouncement with the id of {id}"
                :request="announcementUpdateRequest"
                :response="announcementUpdateResponse"
                auth
            ></doc-item>

            <doc-item
                title="/api/announcements/{id}"
                requestType="DELETE"
                description="Deletes annnouncement with the id of {id}"
                :request="announcementDeleteRequest"
                :response="announcementDeleteResponse"
                auth
            ></doc-item>

            <doc-item
                title="/api/search/tag/{id}"
                requestType="GET"
                description="Retrieves all annnouncements that have a tag with the id of {id}"
                :response="tagSearchResponse"
            ></doc-item>

            <doc-item
                title="/api/search/author/{id}"
                requestType="GET"
                description="Retrieves all annnouncements that have a specific author with the id of {id}"
                :response="wordSearchRespose"
            ></doc-item>
        </div>

        <!-- Tags -->
        <div class="block">
            <div class="block">
                <h3 class="subtitle is-3 has-text-centered">Tags</h3>
            </div>
            <doc-item
                title="/api/tags"
                requestType="GET"
                description="Retrieves all tags"
                :response="tagResponseAll"
            ></doc-item>

            <doc-item
                title="/api/filtertags?users[]=userId1&amp;users[]=userId2&amp;q=%22words+words%22"
                requestType="GET"
                description="/api/filtertags - Retrieves all tags with announcement count for all the announcements and all of their children without any params. 
                /api/filtertags?params - Retrieves all tags with announcement count for filtered announcements and all of their children. Param example can be seen in the title.
                 Every param is optional and doesn't require to be added."
                :response="filtertagResponse"
            ></doc-item>

            <doc-item
                title="/api/tags/{id}"
                requestType="GET"
                description="Retrieves info of tag with id of {id}"
                :response="tagResponseSingle"
            ></doc-item>

            <doc-item
                title="/api/tags"
                requestType="POST"
                description="Creates a new tag"
                :request="tagCreateRequest"
                :response="tagCreateResponse"
                auth
                admin
            ></doc-item>

            <doc-item
                title="/api/tags/{id}"
                requestType="PUT"
                description="Updates info of tag with id of {id}"
                :request="tagUpdateRequest"
                :response="tagUpdateResponse"
                auth
                admin
            ></doc-item>

            <doc-item
                title="/api/tags/{id}"
                requestType="DELETE"
                description="Deletes tag with id of {id}"
                :request="tagDeleteRequest"
                :response="tagDeleteResponse"
                auth
                admin
            ></doc-item>
        </div>

        <!-- Authentication -->
        <div class="block">
            <div class="block">
                <h3 class="subtitle is-3 has-text-centered">Authentication</h3>
            </div>
            <doc-item
                title="/api/auth/login"
                requestType="POST"
                description="Logs user in"
                :request="userLoginRequest"
                :response="userLoginResponse"
            ></doc-item>
            <doc-item
                title="/api/auth/logout"
                requestType="GET"
                description="Logs user out"
                auth
                :request="userLogoutRequest"
                :response="userLogoutResponse"
            ></doc-item>
            <doc-item
                title="/api/auth/user"
                requestType="GET"
                description="Retrieves info about user"
                auth
                :response="authUserResponse"
            ></doc-item>
            <doc-item
                title="/api/auth/user/notifications"
                requestType="GET"
                description="Retrieves a user's notifications"
                :response="userNotificationsResponse"
                auth
            ></doc-item>
            <doc-item
                title="/api/auth/subscribe"
                requestType="POST"
                description="Subscribes user to announcement tags"
                :request="userSubscribeRequest"
                :response="userSubscribeResponse"
                auth
            ></doc-item>
            <doc-item
                title="/api/auth/authors?tags[]=tagId1&amp;tags[]=tagId2&amp;q=%22words+words%22"
                requestType="GET"
                description="/api/auth/authors - Retrieves list of authors with announcement count for all the announcements without any params.
                /api/auth/authors?params - Retrieves list of authors with announcement count for filtered announcements Param example can be seen in the title.
                 Every param is optional and doesn't require to be added."
                :response="authAuthorsResponse"
            ></doc-item>
        </div>

        <!-- Models -->
        <div class="block">
            <div class="block">
                <h3 class="subtitle is-3 has-text-centered">Models</h3>
            </div>
            <div class="box has-background-grey-lighter">
                <div class="columns is-multiline">
                    <div class="column is-full">
                        <model-item
                            title="Announcement"
                            v-bind:fields="announcementModel"
                        ></model-item>
                    </div>
                    <div class="column is-full">
                        <model-item
                            title="Tag"
                            v-bind:fields="tagModel"
                        ></model-item>
                    </div>
                    <div class="column is-full">
                        <model-item
                            title="Attachment"
                            v-bind:fields="attachmentModel"
                        ></model-item>
                    </div>
                    <div class="column is-full">
                        <model-item
                            title="User"
                            v-bind:fields="userModel"
                        ></model-item>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            tagModel: {
                id: "bigint(20) auto_increment",
                title: "varchar(255)",
                is_public: "boolean",
                parent_id: "bigint(20)",
                created_at: "timestamp",
                updated_at: "timestamp"
            },
            announcementModel: {
                id: "bigint(20) auto_increment",
                title: "varchar(255)",
                body: "mediumtext",
                is_event: "boolean",
                event_start_time: "timestamp",
                event_end_time: "timestamp",
                event_location: "varchar(255)",
                created_at: "timestamp",
                updated_at: "timestamp",
                deleted_at: "timestamp",
                is_pinned: "boolean",
                gmaps: "boolean",
                has_eng: "boolean",
                eng_title: "varchar(255)",
                eng_body: "mediumtext",
                pinned_until: "timestamp",
                tags: "Tag []",
                attachments: "Attachment []"
            },
            attachmentModel: {
                id: "bigint(20)",
                announcement_id: "bigint(20)",
                filename: "varchar(255)",
                content: "mediumblob",
                filesize: "int(10)",
                mime_type: "varchar(255)",
                created_at: "timestamp",
                updated_at: "timestamp",
                deleted_at: "timestamp"
            },
            userModel: {
                id: "bigint(20)",
                name: "varchar(255)",
                email: "varchar(255)",
                created_at: "timestamp",
                updated_at: "timestamp",
                is_author: "tinyint(1)",
                is_admin: "tinyint(1)",
                last_login_at: "datetime",
                uid: "varchar(255)"
            },
            tagResponseAll: `{ "data": [ { "id": 6, "title": "1102 Δομημένος Προγραμματισμός Εργαστήριο", "parent_id": 4, "is_public": 0 }, { "id": 5, "title": "1102 Δομημένος Προγραμματισμός Θεωρία", "parent_id": 4, "is_public": 0 }, { "id": 4, "title": "1102 Δομημένος Προγραμματισμός", "parent_id": 2, "is_public": 0 }, { "id": 3, "title": "Εκδηλώσεις", "parent_id": 1, "is_public": 1 }, { "id": 2, "title": "Εξάμηνο Α", "parent_id": 1, "is_public": 0 }, { "id": 1, "title": "Όλες οι ανακοινώσεις", "parent_id": null, "is_public": 0 } ] }`,
            filtertagResponse: `[{"id":163,"title":"1ο Έτος","parent_id":1,"is_public":false,"created_at":"2021-03-30 16:46:26","updated_at":"null","deleted_at":null,"announcements_count":785,"children_recursive":[{"id":163,"title":"1ο Εξάμηνο","parent_id":163,"is_public":false,"created_at":"2020-05-11 21:15:15","updated_at":"2020-05-11 21:15:15","deleted_at":null,"announcements_count":34,"children_recursive":[]}]},{"id":18748,"title":"ImseLab","parent_id":1,"is_public":true,"created_at":"2021-07-19 09:11:04","updated_at":"2021-07-19 09:11:04","deleted_at":null,"announcements_count":0,"children_recursive":[]},{"id":160,"title":"Test board","parent_id":1,"is_public":true,"created_at":"2021-03-30 16:45:13","updated_at":"2021-03-30 16:45:13","deleted_at":null,"announcements_count":0,"children_recursive":[]},{"id":19,"title":"\u0386\u03bb\u03bb\u03b1 \u03b4\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03bd\u03ad\u03b1","parent_id":1,"is_public":true,"created_at":"2020-05-11 21:15:15","updated_at":"2020-05-11 21:15:15","deleted_at":null,"announcements_count":19,"children_recursive":[]}]`,
            tagResponseSingle: `{ "data": { "id": 2, "title": "Εξάμηνο Α", "parent_id": 1, "is_public": 0 } }`,
            announcementsResponse: `{"data":[{"id":5,"title":"\u0391\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7","eng_title":null,"body":"<p>\u0394\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03b1\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7<\/p>","eng_body":null,"has_eng":0,"created_at":"2020-04-13 20:37","updated_at":"2020-04-17 18:57","is_pinned":0,"pinned_until":null,"is_event":1,"event_start_time":"2020-04-29 00:00","event_end_time":"2020-04-30 00:00","event_location":"\u0394\u0399\u03a0\u0391\u0395 \u03a3\u03af\u03bd\u03b4\u03bf\u03c2","gmaps":1,"tags":[{"id":19,"title":"\u0386\u03bb\u03bb\u03b1 \u03b4\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03bd\u03ad\u03b1","parent_id":1,"is_public":1}],"attachments":[],"author":{"name":"Nikolaos Christos Nikolaidis","id":2}}],"links":{"first":"https:\/\/aboard.iee.ihu.gr\/api\/announcements?page=1","last":"https:\/\/aboard.iee.ihu.gr\/api\/announcements?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"https:\/\/aboard.iee.ihu.gr\/api\/announcements","per_page":10,"to":1,"total":1}}`,
            singleAnnouncementResponse: `{"data":{"id":5,"title":"\u0391\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7","eng_title":null,"body":"<p>\u0394\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03b1\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7<\/p>","eng_body":null,"has_eng":0,"created_at":"2020-04-13 20:37","updated_at":"2020-04-17 18:57","is_pinned":0,"pinned_until":null,"is_event":1,"event_start_time":"2020-04-29 00:00","event_end_time":"2020-04-30 00:00","event_location":"\u0394\u0399\u03a0\u0391\u0395 \u03a3\u03af\u03bd\u03b4\u03bf\u03c2","gmaps":1,"tags":[{"id":19,"title":"\u0386\u03bb\u03bb\u03b1 \u03b4\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03bd\u03ad\u03b1","parent_id":1,"is_public":1}],"attachments":[],"author":{"name":"Nikolaos Christos Nikolaidis","id":2}}}`,
            tagSearchResponse: `{"data":[{"id":5,"title":"\u0391\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7","eng_title":null,"body":"<p>\u0394\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03b1\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7<\/p>","eng_body":null,"has_eng":0,"created_at":"2020-04-13 20:37","updated_at":"2020-04-17 18:57","is_pinned":0,"pinned_until":null,"is_event":1,"event_start_time":"2020-04-29 00:00","event_end_time":"2020-04-30 00:00","event_location":"\u0394\u0399\u03a0\u0391\u0395 \u03a3\u03af\u03bd\u03b4\u03bf\u03c2","gmaps":1,"tags":[{"id":19,"title":"\u0386\u03bb\u03bb\u03b1 \u03b4\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03bd\u03ad\u03b1","parent_id":1,"is_public":1}],"attachments":[],"author":{"name":"Nikolaos Christos Nikolaidis","id":2}}],"links":{"first":"https:\/\/aboard.iee.ihu.gr\/api\/search\/tag\/19?page=1","last":"https:\/\/aboard.iee.ihu.gr\/api\/search\/tag\/19?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"https:\/\/aboard.iee.ihu.gr\/api\/search\/tag\/19","per_page":10,"to":1,"total":1}}`,
            wordSearchRespose: `{"data":[{"id":5,"title":"\u0391\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7","eng_title":null,"body":"<p>\u0394\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03b1\u03bd\u03b1\u03ba\u03bf\u03af\u03bd\u03c9\u03c3\u03b7<\/p>","eng_body":null,"has_eng":0,"created_at":"2020-04-13 20:37","updated_at":"2020-04-17 18:57","is_pinned":0,"pinned_until":null,"is_event":1,"event_start_time":"2020-04-29 00:00","event_end_time":"2020-04-30 00:00","event_location":"\u0394\u0399\u03a0\u0391\u0395 \u03a3\u03af\u03bd\u03b4\u03bf\u03c2","gmaps":1,"tags":[{"id":19,"title":"\u0386\u03bb\u03bb\u03b1 \u03b4\u03b7\u03bc\u03cc\u03c3\u03b9\u03b1 \u03bd\u03ad\u03b1","parent_id":1,"is_public":1}],"attachments":[],"author":{"name":"Nikolaos Christos Nikolaidis","id":2}}],"links":{"first":"https:\/\/aboard.iee.ihu.gr\/api\/search\/author\/2?page=1","last":"https:\/\/aboard.iee.ihu.gr\/api\/search\/author\/2?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"https:\/\/aboard.iee.ihu.gr\/api\/search\/author\/2","per_page":10,"to":1,"total":1}}`,
            authUserResponse: `{ "data": { "id": 21, "name": "Nikolaos-Christos Nikolaidis", "email": "it113763@it.teithe.gr", "uid": "it113763", "is_admin": 0, "is_author": 0, "subscriptions": [ { "id": 4, "title": "Eξάμηνο Γ", "parent_id": 1, "is_public": 0 }, { "id": 6, "title": "Eξάμηνο Ε", "parent_id": 1, "is_public": 0 } ], "last_interaction_time": "2020-05-27 23:45:38", "last_login_at": "2020-05-27 23:45:25" } }`,
            userNotificationsResponse: `{"data":[{"id":"c0783866-91ba-4b4a-bf02-16a1421d5d08","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-21 22:14"},{"id":"e3fdd0b4-7cce-483d-a734-5c26bf208df3","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-21 21:20"},{"id":"7c4ae701-99c7-45c1-af5c-9db3228a19c2","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-21 20:51"},{"id":"611929be-b0f5-4f27-87b7-7189d4b7c292","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 23:22"},{"id":"f1d7d105-2134-4076-93a9-c9bc79001dc2","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 23:09"},{"id":"05032396-e1d1-429d-847e-c82799395c6d","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 22:06"},{"id":"fbc7f995-3588-4471-ba31-d62f314a114b","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 21:01"},{"id":"2dd5df85-4706-4702-9919-a434588236f2","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 20:42"},{"id":"49e17d84-e0ce-436e-8c43-1cb6613a2ea6","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 17:41"},{"id":"f72e8850-7216-45cd-a3bc-08d29f307541","data":{"type":"user.login","id":2,"user":"Nikolaos Christos Nikolaidis"},"created_at":"2020-04-20 17:39"}],"links":{"first":"https:\/\/aboard.iee.ihu.gr\/api\/auth\/user\/notifications?page=1","last":"https:\/\/aboard.iee.ihu.gr\/api\/auth\/user\/notifications?page=7","prev":null,"next":"https:\/\/aboard.iee.ihu.gr\/api\/auth\/user\/notifications?page=2"},"meta":{"current_page":1,"from":1,"last_page":7,"path":"https:\/\/aboard.iee.ihu.gr\/api\/auth\/user\/notifications","per_page":10,"to":10,"total":69}}`,
            authAuthorsResponse: `[{"id":2,"name":"Nikolaos Christos Nikolaidis", "announcement_count":5}, {"id":3,"name":"George Stinis", "announcement_count":3}, {"id":4,"name":"Pantelis Papadopoylos", "announcement_count":6}]`,
            userLoginRequest: `curl --request POST --url https://aboard.iee.ihu.gr/api/auth/login --data username=it113763@it.teithe.gr --data password=password`,
            userLoginResponse: `{"token_type":"Bearer","expires_in":3600,"access_token":"ACCESS_TOKEN","refresh_token":"REFRESH_TOKEN"}`,
            userLogoutRequest: `curl --request GET --url https://aboard.iee.ihu.gr/api/auth/logout --header 'authorization: Bearer ACCESS_TOKEN'`,
            userLogoutResponse: `{"message": "Logout success"}`,
            userSubscribeRequest: `curl 'http://aboard.iee.ihu.gr/api/auth/subscribe' -H 'Authorization: Bearer ACCESS_TOKEN --data-binary '{"tags":"[4,6,108,107]"}'`,
            userSubscribeResponse: `{"id":21,"subscriptions":[{"id":4,"title":"E\u03be\u03ac\u03bc\u03b7\u03bd\u03bf \u0393","parent_id":1,"is_public":0,"created_at":"2020-05-11 21:15:15","updated_at":"2020-05-11 21:15:15","deleted_at":null,"pivot":{"user_id":21,"tag_id":4}},{"id":6,"title":"E\u03be\u03ac\u03bc\u03b7\u03bd\u03bf \u0395","parent_id":1,"is_public":0,"created_at":"2020-05-11 21:15:15","updated_at":"2020-05-11 21:15:15","deleted_at":null,"pivot":{"user_id":21,"tag_id":6}},{"id":107,"title":"1974 \u0394\u03bf\u03c1\u03c5\u03c6\u03bf\u03c1\u03b9\u03ba\u03ad\u03c2 \u0395\u03c0\u03b9\u03ba\u03bf\u03b9\u03bd\u03c9\u03bd\u03af\u03b5\u03c2","parent_id":10,"is_public":0,"created_at":"2020-05-11 21:15:15","updated_at":"2020-05-11 21:15:15","deleted_at":null,"pivot":{"user_id":21,"tag_id":107}},{"id":108,"title":"1975 \u03a4\u03b5\u03c7\u03bd\u03bf\u03bb\u03bf\u03b3\u03af\u03b1 \u03a0\u03bf\u03bb\u03c5\u03bc\u03ad\u03c3\u03c9\u03bd","parent_id":10,"is_public":0,"created_at":"2020-05-11 21:15:15","updated_at":"2020-05-11 21:15:15","deleted_at":null,"pivot":{"user_id":21,"tag_id":108}}]}`,
            tagCreateRequest: `curl 'http://aboard.iee.ihu.gr/api/tags'  -H 'Authorization: Bearer ACCESS_TOKEN' --data-binary '{"title":"Test","parent_id":2,"is_public":false}'`,
            tagCreateResponse: `{"title":"Test","is_public":false,"parent_id":2,"updated_at":"2020-05-28 21:38:38","created_at":"2020-05-28 21:38:38","id":109}`,
            tagUpdateRequest: `curl 'http://aboard.iee.ihu.gr/api/tags/109' -X 'PUT' -H 'Authorization: Bearer ACCESS_TOKEN --data-binary '{"title":"Test edit","parent_id":10,"is_public":true}'`,
            tagUpdateResponse: `{"data":{"id":109,"title":"Test edit","parent_id":10,"is_public":1}}`,
            tagDeleteRequest: `curl 'http://aboard.iee.ihu.gr/api/tags/109' -X 'DELETE' -H 'Authorization: Bearer ACCESS_TOKEN'`,
            tagDeleteResponse: `{"data":{"all": "tags", "not shown": "to keep this page as short as possible"}}`,
            announcementCreateRequest: `curl 'http://aboard.iee.ihu.gr/api/announcements' -H 'Accept: application/json' 
  -H 'Authorization: Bearer ACCESS_TOKEN'
  -H 'Content-Type: multipart/form-data;
  --data-binary $' name="attachments[0]"; filename="test.txt"\r\nContent-Type: text/plain\r\n\r\n\r\n name="title"\r\n\r\nTest title\r\n name="body"\r\n\r\n<p>Test announcement</p>\r\n name="is_pinned"\r\n\r\ntrue\r\n name="pinned_until"\r\n\r\n"2020-05-31 00:00"\r\n name="is_event"\r\n\r\ntrue\r\n name="event_location"\r\n\r\nTEITHE\r\n name="event_start_time"\r\n\r\n"2020-05-28 00:00"\r\n name="event_end_time"\r\n\r\n"2020-05-31 00:00"\r\n name="gmaps"\r\n\r\ntrue\r\n name="tags"\r\n\r\n[108]\r\n name="has_eng"\r\n\r\ntrue\r\n name="eng_title"\r\n\r\nTest title (English)\r\n name="eng_body"\r\n\r\n<p>Test announcement (English)</p>\r\n--\r\n'`,
            announcementCreateResponse: `{"data":{"id":5,"title":"Test title","eng_title":"Test title (English)","body":"<p>Test announcement<\/p>","eng_body":"<p>Test announcement (English)<\/p>","has_eng":true,"created_at":"2020-05-28 22:06","updated_at":"2020-05-28 22:06","is_pinned":true,"pinned_until":"2020-05-31 00:00","is_event":true,"event_start_time":"2020-05-28 00:00","event_end_time":"2020-05-31 00:00","event_location":"TEITHE","gmaps":true,"tags":[{"id":10,"title":"E\u03be\u03ac\u03bc\u03b7\u03bd\u03bf \u0398","parent_id":1,"is_public":0},{"id":108,"title":"1975 \u03a4\u03b5\u03c7\u03bd\u03bf\u03bb\u03bf\u03b3\u03af\u03b1 \u03a0\u03bf\u03bb\u03c5\u03bc\u03ad\u03c3\u03c9\u03bd","parent_id":10,"is_public":0}],"attachments":[{"id":4,"announcement_id":5,"filename":"test.txt","content":"","filesize":0,"mime_type":"inode\/x-empty"}],"author":{"name":"Nikolaos-Christos Nikolaidis","id":21}}}`,
            announcementUpdateRequest: `curl 'http://aboard.iee.ihu.gr/api/announcements/5' \
  -H 'Authorization: Bearer ACCESS_TOKEN' \
  -H 'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary8AHxBKN9Wu7AexMh' \
  --data-binary $'------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="title"\r\n\r\nTest title (edit)\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="body"\r\n\r\n<p>Test announcement (edit)</p>\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="is_pinned"\r\n\r\nfalse\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="is_event"\r\n\r\nfalse\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="tags"\r\n\r\n[10,108]\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="has_eng"\r\n\r\nfalse\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh\r\nContent-Disposition: form-data; name="_method"\r\n\r\nput\r\n------WebKitFormBoundary8AHxBKN9Wu7AexMh--\r\n'`,
            announcementUpdateResponse: `{"data":{"id":5,"title":"Test title (edit)","eng_title":null,"body":"<p>Test announcement (edit)<\/p>","eng_body":null,"has_eng":false,"created_at":"2020-05-28 22:06","updated_at":"2020-05-28 22:22","is_pinned":false,"pinned_until":null,"is_event":false,"event_start_time":null,"event_end_time":null,"event_location":null,"gmaps":null,"tags":[{"id":10,"title":"E\u03be\u03ac\u03bc\u03b7\u03bd\u03bf \u0398","parent_id":1,"is_public":0},{"id":108,"title":"1975 \u03a4\u03b5\u03c7\u03bd\u03bf\u03bb\u03bf\u03b3\u03af\u03b1 \u03a0\u03bf\u03bb\u03c5\u03bc\u03ad\u03c3\u03c9\u03bd","parent_id":10,"is_public":0}],"attachments":[],"author":{"name":"Nikolaos-Christos Nikolaidis","id":21}}}`,
            announcementDeleteRequest: `curl 'http://aboard.iee.ihu.gr/api/announcements/5' -X 'DELETE' -H 'Authorization: Bearer ACCESS_TOKEN'`,
            announcementDeleteResponse: `{"data":{"all": "announcements", "not shown": "to keep this page as short as possible"}}`
        };
    }
};
</script>

<style></style>f
