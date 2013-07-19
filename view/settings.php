<?php

if (isset($_POST['submit'])) {

    TwitterAPI::update_settings($_POST);

}

?><div class="wrap">

    <div id="icon-options-general" class="icon32"><br /></div> 
    <h2>
        Twitter API
    </h2>

    <form method="post" action="">
        <table class="form-table">
            <tbody>
        
                <tr valign="top">
                    <th scope="row">
                        <label for="tapi_consumer_key">
                            Consumer key
                        </label>
                    </th>
                    <td>
                        <input name="consumer_key" type="text" id="tapi_consumer_key" value="<?= get_option(TAPI_SLUG.'_consumer_key') ?>" class="regular-text">
                        <p class="description">Required</p>
                    </td>
                </tr>
        
                <tr valign="top">
                    <th scope="row">
                        <label for="tapi_consumer_secret">
                            Consumer secret
                        </label>
                    </th>
                    <td>
                        <input name="consumer_secret" type="text" id="tapi_consumer_secret" value="<?= get_option(TAPI_SLUG.'_consumer_secret') ?>" class="regular-text">
                        <p class="description">Required</p>
                    </td>
                </tr>
        
                <tr valign="top">
                    <th scope="row">
                        <label for="tapi_oauth_access_token">
                            Access token
                        </label>
                    </th>
                    <td>
                        <input name="oauth_access_token" type="text" id="tapi_oauth_access_token" value="<?= get_option(TAPI_SLUG.'_oauth_access_token') ?>" class="regular-text">
                        <p class="description">Required</p>
                    </td>
                </tr>
        
                <tr valign="top">
                    <th scope="row">
                        <label for="tapi_consumer_key">
                            Access token secret
                        </label>
                    </th>
                    <td>
                        <input name="oauth_access_token_secret" type="text" id="tapi_oauth_access_token_secret" value="<?= get_option(TAPI_SLUG.'_oauth_access_token_secret') ?>" class="regular-text">
                        <p class="description">Required</p>
                    </td>
                </tr>
        
                <tr valign="top">
                    <th scope="row">
                        <label for="tapi_use_cache">
                            Use Cache
                        </label>
                    </th>
                    <td>
                        <select name="use_cache" type="number" id="tapi_use_cache">
                            <option value="Y" <? if (get_option(TAPI_SLUG.'_use_cache') === 'Y') echo 'selected' ?>>Yes</option>
                            <option value="N" <? if (get_option(TAPI_SLUG.'_use_cache') === 'N') echo 'selected' ?>>No</option>
                        </select>
                    </td>
                </tr>
        
                <tr valign="top">
                    <th scope="row">
                        <label for="tapi_expiration_time">
                            Expiration Time (minutes)
                        </label>
                    </th>
                    <td>
                        <input name="expiration_time" type="number" id="tapi_expiration_time" value="<?= get_option(TAPI_SLUG.'_expiration_time') ?>" class="regular-text">
                    </td>
                </tr>

            </tbody>
        </table>
        <? wp_nonce_field('update_twitter_api', 'update') ?>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>

</div>