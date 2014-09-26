package de.vogella.android.ownservice.local;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.LinkedList;
import java.util.List;
import java.util.Random;

import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.location.LocationListener;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Binder;
import android.os.Bundle;
import android.os.IBinder;
import android.os.Message;
import android.os.Messenger;
import android.os.RemoteException;
import android.util.Log;
import android.content.Intent;
import android.content.SharedPreferences.Editor;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.math.BigInteger;




public class LocalWordService extends Service implements LocationListener
{
	private final IBinder mBinder = new MyBinder();
	
	private Messenger mMessenger;
	
	private static ArrayList<String> list = new ArrayList<String>();
	
	private static LinkedList<Location> locationlist = new LinkedList<Location>();
	private static final int MAX_LOCATION_LIST = 2500;
	
	//public locationList myList = new locationList();
	
	private Thread mythread = null;
	private Thread locthread = null;

	// flag for GPS status
	boolean isGPSEnabled = false;

	// flag for network status
	boolean isNetworkEnabled = false;

	// flag for GPS status
	boolean canGetLocation = false;

	Location lastlocation; // location
	Location currentlocation; // location
	
	
	double latitude; // latitude
	double longitude; // longitude

	// The minimum distance to change Updates in meters
	private static final long MIN_DISTANCE_CHANGE_FOR_UPDATES = 5;//10; // 10 meters

	// The minimum time between updates in milliseconds
	private static final long MIN_TIME_BW_UPDATES = 1000 * 30 * 1; // 1 minute

	// Declaring a Location Manager
	protected LocationManager locationManager;
	
	String httpcontext;
	
	
	private boolean isStop = false;
	
	//private String username = null;
	//private String password = null;
	private String username = "test3";
	private String password = "test3";
	
	private String pref_time_key = "rheatime";
	private String pref_total_key = "rheatotal";
	private String pref_send_key = "rheasend";
	private SharedPreferences configs;
	private int rhea_gps_total;
	private int rhea_gps_send;
	private long rhea_gps_time;
	
	 
	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {

		Log.v("Localword","Get my location....");
		
		//initGPSFile();
		
		getLocation();
		
		getLocationForce();

		return Service.START_NOT_STICKY;
	}
	
    @Override
    public void onCreate() {
    	Log.v("Localword","create location service....");
    	initGPSFile();
    }
    
    @Override
    public void onDestroy() {
    	Log.v("Localword","destroy location service....");
    	
    }

    
    public synchronized LinkedList<Location> getLocationList()
    {
    	return locationlist;
    }
    
	
	
	public void processLocation(Location location)
	{
		if(location!=null && isBetterLocation(lastlocation,location))
		{
			
			addLocationFile(location);
			
			//send to UI
			sendMsgtoApp(location);
			
			latitude = 	location.getLatitude();
			longitude = location.getLongitude();

			

			if(httpcontext == null)
				httpcontext = "400";
			list.add(Double.toString(longitude) +"," + 
				Double.toString(latitude)+ ","+ httpcontext);
			if (list.size() >= MAX_LOCATION_LIST) 
				list.remove(0);
			
			// if the list is full, then remove first one.
			if(getLocationList().size()>=MAX_LOCATION_LIST)
				getLocationList().remove();			
			getLocationList().add(location);
			lastlocation = location;
			
			StringBuffer data = new StringBuffer(256);
			data.append("ADD GPS=");
			data.append(location.getLatitude()+",");
			data.append(location.getLongitude());
			data.append(",");
			data.append(httpcontext);
			data.append("\n");
			Log.w("Localword",data.toString());	
			
			Log.v("Localword","send my location....");
			
			if(mythread != null)
			{
				if(mythread.isAlive())
					return;
			}
			
			sendtoserverbg();			
		}
	}
	
	
	
	protected boolean isBetterLocation(Location location, Location currentBestLocation) 
	{
		if(location == null ||currentBestLocation==null)
			return true;
		
		if(location.distanceTo(currentBestLocation) > MIN_DISTANCE_CHANGE_FOR_UPDATES)
			return true;
		else
			return false;
	}
	
	
	public void sendtoserverbg()
	{
		
		if(!isNetworkEnable())
			return;
		
		Runnable sendrunnable = new Runnable() {
        public void run() {
        	senddatatoserver();
        	}

        };
   
        mythread = new Thread(sendrunnable);
        mythread.start();
	
	}

	
	public boolean isNetworkEnable()
	{
		ConnectivityManager conMgr =  (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo activeNetwork = conMgr.getActiveNetworkInfo();
		if(activeNetwork ==null)
			return false;
		
		if(activeNetwork.isConnected())
			return true;
		else
			return false;
	}
	
	
	
	public void senddatatoserver()
	{	
	
		while(rhea_gps_send < rhea_gps_total)
		{
			//Location location = getLocationList().getFirst();
			Location location =  getLocationPoint(rhea_gps_send + 1);
			StringBuffer sb_url = new StringBuffer(256);
			sb_url.append("http://rhea.sinaapp.com/senddata.php?");
			sb_url.append("n=" + username);
			sb_url.append("&p=" + encodeMD5(password));
			sb_url.append("&x=");
			sb_url.append(location.getLongitude());
			sb_url.append("&y=");
			sb_url.append(location.getLatitude());
			sb_url.append("&type=1");
			
			Log.d("Localword",sb_url.toString());
		

			if (!isNetworkEnable()) 
			{
				httpcontext = "400";
				Log.d("Localword", "network is not ok");
				break;
			    //notify user you are online
			} 
			
			
			DefaultHttpClient httpclient = new DefaultHttpClient();
		    try {
		      HttpGet httpget = new HttpGet(sb_url.toString());
		      httpget.addHeader("User-Agent", "ie 6");
		      HttpResponse response = httpclient.execute(httpget);
		      HttpEntity entity = response.getEntity();
		      
		      if (entity != null) 
		      {
	
		            // A Simple JSON Response Read
		            InputStream instream = entity.getContent();
		            httpcontext= convertStreamToString(instream);
		           
		            // send data ok
		            if(httpcontext != null && httpcontext.startsWith("OK"))
		            {
		            	//getLocationList().remove();
		            	//rhea_gps_send = rhea_gps_send +1;
		            	updateLocationSendOK();
		            	
		            }
		            else
		            {
		            	break;
		            }
		            
		            Log.d("Localword", "httpget:" + httpcontext);
		            // now you have the string representation of the HTML request
		            instream.close();
		       }
		      else
		      {
		    	  httpcontext = Integer.toString(response.getStatusLine().getStatusCode());
		    	  Log.d("Localword", "httpget error:" + httpcontext);
		    	  if(httpcontext.isEmpty())
		    		  httpcontext = "400";
		    	  break;
		    		  
		      }
		    
		    } catch (IOException e) 
		    {
		    	httpcontext = "400";	
		        e.printStackTrace();
		        break;
		      //return null;
		    }
		    
		}
				
	}
	
	
	
	public void senddatatoserver_old()
	{	
	
		while(getLocationList().size() > 0)
		{
			Location location = getLocationList().getFirst();
			StringBuffer sb_url = new StringBuffer(256);
			sb_url.append("http://rhea.sinaapp.com/senddata.php?");
			sb_url.append("n=" + username);
			sb_url.append("&p=" + encodeMD5(password));
			sb_url.append("&x=");
			sb_url.append(location.getLongitude());
			sb_url.append("&y=");
			sb_url.append(location.getLatitude());
			sb_url.append("&type=1");
			
			Log.d("Localword",sb_url.toString());
		
			final ConnectivityManager conMgr =  (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
			final NetworkInfo activeNetwork = conMgr.getActiveNetworkInfo();
			if (activeNetwork ==null ||((activeNetwork!=null)&&!activeNetwork.isConnected())) 
			{
				httpcontext = "400";
				Log.d("Localword", "network is not ok");
				break;
			    //notify user you are online
			} 
			
			
			DefaultHttpClient httpclient = new DefaultHttpClient();
		    try {
		      HttpGet httpget = new HttpGet(sb_url.toString());
		      httpget.addHeader("User-Agent", "ie 6");
		      HttpResponse response = httpclient.execute(httpget);
		      HttpEntity entity = response.getEntity();
		      
		      if (entity != null) 
		      {
	
		            // A Simple JSON Response Read
		            InputStream instream = entity.getContent();
		            httpcontext= convertStreamToString(instream);
		           
		            // send data ok
		            if(httpcontext != null && httpcontext.startsWith("OK"))
		            {
		            	getLocationList().remove();
		            }
		            else
		            {
		            	break;
		            }
		            
		            Log.d("Localword", "httpget:" + httpcontext);
		            // now you have the string representation of the HTML request
		            instream.close();
		       }
		      else
		      {
		    	  httpcontext = Integer.toString(response.getStatusLine().getStatusCode());
		    	  Log.d("Localword", "httpget error:" + httpcontext);
		    	  if(httpcontext.isEmpty())
		    		  httpcontext = "400";
		    	  break;
		    		  
		      }
		    
		    } catch (IOException e) 
		    {
		    	httpcontext = "400";	
		        e.printStackTrace();
		        break;
		      //return null;
		    }
		    
		}
				
	}
	

	
	private static String convertStreamToString(InputStream is) {
	    /*
	     * To convert the InputStream to String we use the BufferedReader.readLine()
	     * method. We iterate until the BufferedReader return null which means
	     * there's no more data to read. Each line will appended to a StringBuilder
	     * and returned as String.
	     */
	    BufferedReader reader = new BufferedReader(new InputStreamReader(is));
	    StringBuilder sb = new StringBuilder();

	    String line = null;
	    try {
	        while ((line = reader.readLine()) != null) {
	            sb.append(line + "\n");
	        }
	    } catch (IOException e) {
	        e.printStackTrace();
	    } finally {
	        try {
	            is.close();
	        } catch (IOException e) {
	            e.printStackTrace();
	        }
	    }
	    return sb.toString();
	}
	
	public Location getLocation() {
		try {
			locationManager = (LocationManager) this.getSystemService(Context.LOCATION_SERVICE);

			// getting GPS status
			isGPSEnabled = locationManager
					.isProviderEnabled(LocationManager.GPS_PROVIDER);

			// getting network status
			isNetworkEnabled = locationManager
					.isProviderEnabled(LocationManager.NETWORK_PROVIDER);

			if (!isGPSEnabled && !isNetworkEnabled) 
			{
			
				// no network provider is enabled
				currentlocation = null;
			} 
			else
			{
				//this.canGetLocation = true;
				
				// if GPS Enabled get lat/long using GPS Services
				if (isGPSEnabled) 
				{
						locationManager.requestLocationUpdates(
								LocationManager.GPS_PROVIDER,
								MIN_TIME_BW_UPDATES,
								MIN_DISTANCE_CHANGE_FOR_UPDATES, this);
						Log.d("GPS Enabled", "GPS location start...");						

				}
				
				
				if (isNetworkEnabled) 
				{
				
					locationManager.requestLocationUpdates(
							LocationManager.NETWORK_PROVIDER,
							MIN_TIME_BW_UPDATES,
							MIN_DISTANCE_CHANGE_FOR_UPDATES, this);
					
					/*
					locationManager.requestLocationUpdates(
							LocationManager.NETWORK_PROVIDER,
							0,
							1, this);
					*/
					Log.d("Network", "Network location start.");

				}

			}

		} catch (Exception e) {
			e.printStackTrace();
		}

		return currentlocation;
	}
		
	
	public void getLocationForce()
	{
		Location location = null;
		Location locationgps = null;
		Location locationnet = null;
		
		if (locationManager != null) 
		{
			if(isGPSEnabled)
			{
				locationgps = locationManager
					.getLastKnownLocation(LocationManager.GPS_PROVIDER);
				if(locationgps != null)
					location = locationgps;
			}
			
			if(isNetworkEnabled)
			{
				locationnet = locationManager
						.getLastKnownLocation(LocationManager.NETWORK_PROVIDER);
				if(locationnet != null)
					location = locationnet;				
			}
			
		}
		
		if(locationgps != null && locationnet != null)
		{
			if(locationnet.getTime() > locationgps.getTime() + 1000 * 60 * 3)  //3mins
				location = locationnet;
		}
		
		
		
		
		if(location == null)
			return;
	
		currentlocation = location;		

		processLocation(location);
	}
	
	
	
	@Override
	public IBinder onBind(Intent arg0) {
		return mBinder;
	}

	public class MyBinder extends Binder {
		LocalWordService getService() {
			return LocalWordService.this;
		}
	}

	public List<String> getWordList() {
		Log.v("Localword","get data from my service....\n");
		return list;
	}
	
	
	public void setMessagerHandler(Messenger msg)
	{
		Log.v("Localword","Set messager handler");
		mMessenger  = msg;
	}
	
	public void clearMessagerHandler()
	{
		mMessenger = null;
		Log.v("Localword","clear messager handler");
	}
	
	
	public void sendMsgtoApp(Location location)
	{
		if(mMessenger == null)
			return;
		
		try {
			
			Message msg = Message.obtain();
			msg.what = 0x01;
			msg.obj = location;
			mMessenger.send(msg);
	    } 
		catch (RemoteException e) 
	    {
	    	mMessenger = null;
	    }
	}
	
	
	public void stopMyLocalService()
	{
		isStop = true;
	}
	
	
	@Override
	public void onLocationChanged(Location location) {
		Log.w("Localword","GPS data is changed\n");
		//currentlocation = location;		
		//sendMsgtoApp(location);
		//processLocation(location);
	}

	
	public void convertLocationBg()
	{
		Runnable convertrunnable = new Runnable() {
        public void run() {
        	convertLoction();
        	}

        };
   
        locthread = new Thread(convertrunnable);
        locthread.start();
	
	}
	
	public void convertLoction()
	{
		//double mapx = location.getLongitude();
		//double mapy = location.getLatitude();
		
		//http://rhea.sinaapp.com/get.php?x=121.592445&y=31.191768&type=1
		StringBuffer sb_url = new StringBuffer(256);
		sb_url.append("http://rhea.sinaapp.com/get.php?x=");
		sb_url.append(currentlocation.getLongitude());
		sb_url.append("&y=");
		sb_url.append(currentlocation.getLatitude());
		sb_url.append("&type=1");
		
		
		final ConnectivityManager conMgr =  (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		final NetworkInfo activeNetwork = conMgr.getActiveNetworkInfo();
		if (activeNetwork ==null ||((activeNetwork!=null)&&!activeNetwork.isConnected())) 
		{
			httpcontext = "400";
			Log.d("Localword", "network is not ok");
			return;
		    //notify user you are online
		} 
		
		
		DefaultHttpClient httpclient = new DefaultHttpClient();
	    try {
	      HttpGet httpget = new HttpGet(sb_url.toString());
	      httpget.addHeader("User-Agent", "ie 6");
	      HttpResponse response = httpclient.execute(httpget);
	      HttpEntity entity = response.getEntity();
	      
	      if (entity != null) 
	      {

	            // A Simple JSON Response Read
	            InputStream instream = entity.getContent();
	            httpcontext= convertStreamToString(instream);
	            if(httpcontext.isEmpty()||!httpcontext.startsWith("OK"))
	            {
	            	httpcontext = "400";
	            }
	            
	            StringBuffer buffer = new StringBuffer(128);
	            buffer.append("httpget:" + httpcontext );
	            
	            Log.d("Localword", buffer.toString());
	            // now you have the string representation of the HTML request
	            instream.close();
	       }
	      else
	      {
	    	  httpcontext = Integer.toString(response.getStatusLine().getStatusCode());
	    	  Log.d("Localword", "httpget error:" + httpcontext);
	    	  if(httpcontext.isEmpty())
	    		  httpcontext = "400";
	    		  
	      }
	    
	    } catch (IOException e) 
	    {
	    	httpcontext = "400";	
	        e.printStackTrace();
	      //return null;
	    }		
	}
	
	
	@Override
	public void onProviderDisabled(String provider) {
	}

	@Override
	public void onProviderEnabled(String provider) {
	}

	@Override
	public void onStatusChanged(String provider, int status, Bundle extras) {
	}	
		
	
	public String encodeMD5(String str)
	{
		MessageDigest mdEnc = null;
	    try {
	        mdEnc = MessageDigest.getInstance("MD5");
	    } catch (NoSuchAlgorithmException e) { 
	        e.printStackTrace();
	    } // Encryption algorithm
	    
	    mdEnc.update(str.getBytes(), 0, str.length());
	    String md5 = new BigInteger(1, mdEnc.digest()).toString(16) ;
	    return md5;
	}
	
	
	public void initGPSFile()
	{
		configs = getSharedPreferences("rheagps", 0);
		rhea_gps_total = configs.getInt(pref_total_key, 0);
		rhea_gps_send = configs.getInt(pref_send_key, 0);	
		rhea_gps_time = configs.getLong(pref_time_key, 0);	
		
		Log.d("Localword","rhea_gps_time:" + rhea_gps_time + ":" + rhea_gps_total + ":" + rhea_gps_send );
	}
	
	public void checkLocationFile()
	{
		long filetime = configs.getLong(pref_time_key, 0);
		
        Calendar rightnow = Calendar.getInstance();
        int rdata = rightnow.get(Calendar.DATE);
      
        Calendar lastday = Calendar.getInstance();
        lastday.setTimeInMillis(filetime);
        int ldata = lastday.get(Calendar.DATE);
        
        if(rdata != ldata)
        {
        	//reset all data;
			Editor editor = configs.edit();
			long now = rightnow.getTimeInMillis();	
			editor.putLong(pref_time_key, now);
			rhea_gps_send = 0;
			rhea_gps_total = 0;
			editor.putInt(pref_total_key, rhea_gps_total);
			editor.putInt(pref_send_key, rhea_gps_send);
			editor.commit();
        }
        
	}
	
	
	public void addLocationFile(Location loc)
	{
		checkLocationFile();
		
		rhea_gps_total = rhea_gps_total + 1;
		String pref_gps_key = String.valueOf(rhea_gps_total);
		StringBuffer sb = new StringBuffer(256);
		sb.append(loc.getLongitude());
		sb.append(",");
		sb.append(loc.getLatitude());
		sb.append(",");
		sb.append(loc.getAccuracy());
		
		Log.d("Localword","rhea_gps_data:" + rhea_gps_total + ":" + rhea_gps_send + ":" + sb.toString() );
		Editor editor = configs.edit();
		editor.putString(pref_gps_key, sb.toString());
		editor.putInt(pref_total_key, rhea_gps_total);
		editor.commit();
	}
	
	
	public Location getLocationPoint(int index)
	{
		String pref_gps_key = String.valueOf(index);
		String gpsdata = configs.getString(pref_gps_key, null);
		
		if(gpsdata == null)
			return null;
		else
		{
			Location loc = new Location("NETWORK");
			String[] strdata = gpsdata.split(",");
			double x = Double.parseDouble(strdata[0]);
			double y = Double.parseDouble(strdata[1]);
			Log.d("Localword","rhea_gps_data:" + x + ":" + y + ":" + strdata[0] + ":" + strdata[1] );
			float accuracy = Float.parseFloat(strdata[2]);
			loc.setLongitude(x);
			loc.setLatitude(y);
			loc.setAccuracy(accuracy);
			return loc;
		}	
	}
	
	public void updateLocationSendOK()
	{
		rhea_gps_send = rhea_gps_send + 1;
		Editor editor = configs.edit();
		editor.putInt(pref_send_key, rhea_gps_send);
		editor.commit();
	}

}

