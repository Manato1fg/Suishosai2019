/**
 * Copyright Manato1fg(https://twitter.com/manato1fg)
 * 
 * This file is added for managing storage.
 * This file is also under Apache License as other files made by LINE Corp.
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Thanks Line Corp for making such a nice application
 */

package com.linecorp.sample.login.application.controller;

import net.thegreshams.firebase4j.service.Firebase;
import net.thegreshams.firebase4j.model.FirebaseResponse;
import net.thegreshams.firebase4j.error.FirebaseException;
import net.thegreshams.firebase4j.error.JacksonUtilityException;

import org.apache.commons.httpclient.HttpException;
import org.apache.http.NameValuePair;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.message.BasicNameValuePair;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.LinkedHashMap;
import java.util.Map;
import com.fasterxml.jackson.databind.ObjectMapper;

import com.linecorp.sample.login.infra.line.api.v2.response.Profile;

public class StorageManager {

    /**
     * Singleton
     */
    private static StorageManager instance = null;

    //
    private static final String FIREBASE_URL = "https://suishosai-6ec43.firebaseio.com/";
    // private static final String FIREBASE_API_KEY =
    // "AIzaSyBv3lwTcaflADaCXLlnXb6xCnIOWq6pr3g";

    private Firebase firebase = null;

    private StorageManager(String dataName) {

        try {
            this.firebase = new Firebase(FIREBASE_URL + dataName);
        } catch (FirebaseException e) {
            e.printStackTrace();
        }
    }

    public static StorageManager getInstance(String dataName) {
        if (instance == null) {
            instance = new StorageManager(dataName);
        }

        return instance;
    }

    public FirebaseResponse put(String key, String value) {
        Map<String, Object> dataMap = new LinkedHashMap<String, Object>();

        try {
            return firebase.put(dataMap);
        } catch (UnsupportedEncodingException | JacksonUtilityException | FirebaseException e) {
            e.printStackTrace();
        }

        return null;
    }

    public static Profile getProfile(String accessToken) throws Exception{
        HttpURLConnection con = null;
        URL url = new URL("https://api.line.me/v2/profile");
        con = (HttpURLConnection) url.openConnection();
        con.setRequestMethod("GET");
        con.setInstanceFollowRedirects(false);
        con.setRequestProperty("Authorization", "Bearer "+accessToken);
        con.connect();

        String responseData = "";
        InputStream stream = con.getInputStream();
        StringBuffer sb = new StringBuffer();
        String line = "";
        BufferedReader br = new BufferedReader(new InputStreamReader(stream, "UTF-8"));
        while ((line = br.readLine()) != null) {
            sb.append(line);
        }
        try {
            stream.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        responseData = sb.toString();

        ObjectMapper mapper = new ObjectMapper();
        Profile hoge = mapper.readValue(responseData, Profile.class);
        
        return hoge;
    }

    public static void sendReq(String accessToken, String name, String pic) {
        String url = "http://suishosai.starfree.jp/register.php";
        CloseableHttpClient httpClient = HttpClients.createDefault();
        HttpPost request = new HttpPost(url);
        List<NameValuePair> requestParams = new ArrayList<>();
        requestParams.add(new BasicNameValuePair("accessToken", accessToken));
        requestParams.add(new BasicNameValuePair("username", name));
        requestParams.add(new BasicNameValuePair("pic", pic));
        requestParams.add(new BasicNameValuePair("salt", "Suishosai_Somu_73"));
        requestParams.add(new BasicNameValuePair("order", "register"));
        try {
            request.setEntity(new UrlEncodedFormEntity(requestParams));
            httpClient.execute(request);
        } catch (HttpException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

}