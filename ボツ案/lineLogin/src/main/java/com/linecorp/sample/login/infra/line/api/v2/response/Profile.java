/**
 * Copyright Manato1fg(https://twitter.com/manato1fg)
 * 
 * This file is added for managing storage.
 * This file is also under Apache License as other files made by LINE Corp.
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Thanks Line Corp for making such a nice application
 */

package com.linecorp.sample.login.infra.line.api.v2.response;

import com.fasterxml.jackson.annotation.JsonCreator;
import com.fasterxml.jackson.annotation.JsonProperty;

public class Profile {
    public String userId;
    public String displayName;
    public String pictureUrl;
    public String statusMessage;

    @JsonCreator
    public Profile(@JsonProperty("userId") String userId, @JsonProperty("displayName") String displayName, @JsonProperty("pictureUrl") String pictureUrl, @JsonProperty("statusMessage") String statusMessage) {
        this.userId = userId;
        this.displayName = displayName;
        this.pictureUrl = pictureUrl;
        this.statusMessage = statusMessage;

        System.out.println(this.displayName);
    }
}