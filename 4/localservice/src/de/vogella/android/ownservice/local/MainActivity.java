package de.vogella.android.ownservice.local;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.nio.ByteBuffer;
import java.util.ArrayList;

import java.util.Calendar;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.HttpStatus;
import org.apache.http.StatusLine;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlarmManager;
import android.app.ListActivity;
import android.app.PendingIntent;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.ServiceConnection;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.location.Location;
import android.os.AsyncTask;
import android.os.AsyncTask.Status;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.os.Message;
import android.os.Messenger;
import android.preference.PreferenceManager;
import android.util.Base64;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Toast;


import com.baidu.mapapi.BMapManager;
import com.baidu.mapapi.map.LocationData;
import com.baidu.mapapi.map.MapController;
import com.baidu.mapapi.map.MapView;
import com.baidu.mapapi.map.MyLocationOverlay;
import com.baidu.platform.comapi.basestruct.GeoPoint;

public class MainActivity extends Activity {
	private LocalWordService s;

	private Button startbutton; 
	private SharedPreferences sharedPrefs;
	
	/**
	 *  MapView �ǵ�ͼ���ؼ�
	 */
	private MapView mMapView = null;
	private MapController mMapController = null;
	private MyLocationOverlay myLocationOverlay = null;
	private Boolean isMapView = true;
	boolean isRequest = false;//�Ƿ��ֶ���������λ
	boolean isFirstLoc = true;//�Ƿ��״ζ�λ
	private final int MAP_ZOOM = 10;
	
	private String BAIDU_URL = "http://api.map.baidu.com/ag/coord/convert?from=0&to=4&";
	
	public Location conv_loc;
	
	private static Boolean isConvertTaskRun = false;
	private static Boolean isActivity = false;
	//
	private double default_x = 39.945 ;
	private double default_y = 116.404 ;
	
	private AsyncTask<String, String, String> mytask = null;
	
	public static final String PREFS_NAME = "rheaconfig";
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		//setContentView(R.layout.main);
		sharedPrefs = getSharedPreferences(PREFS_NAME, 0);;
		
		getLastLocation();
		initBaiduMap();
		
		
		startbutton = (Button) findViewById(R.id.startbutton);
		if(isStartLocationService())
			startbutton.setText(R.string.stop_name);
		else
			startbutton.setText(R.string.start_name);
		/*
		wordList = new ArrayList<String>();
		adapter = new ArrayAdapter<String>(this,
				android.R.layout.simple_list_item_1, android.R.id.text1,
				wordList);
		setListAdapter(adapter);
		*/
		doBindService();

	}
	
	
	public void initBaiduMap()
	{
        /**
         * ʹ�õ�ͼsdkǰ���ȳ�ʼ��BMapManager.
         * BMapManager��ȫ�ֵģ���Ϊ���MapView���ã�����Ҫ��ͼģ�鴴��ǰ������
         * ���ڵ�ͼ��ͼģ�����ٺ����٣�ֻҪ���е�ͼģ����ʹ�ã�BMapManager�Ͳ�Ӧ������
         */
		EyeApplication app = (EyeApplication)this.getApplication();
        if (app.mBMapManager == null) {
            app.mBMapManager = new BMapManager(this);
            /**
             * ���BMapManagerû�г�ʼ�����ʼ��BMapManager
             */
            app.mBMapManager.init(EyeApplication.strKey,new EyeApplication.MyGeneralListener());
        }
        /**
          * ����MapView��setContentView()�г�ʼ��,��������Ҫ��BMapManager��ʼ��֮��
          */
        setContentView(R.layout.main);
        mMapView = (MapView)findViewById(R.id.bmapView);
        /**
         * ��ȡ��ͼ������
         */
        mMapController = mMapView.getController();
        /**
         *  ���õ�ͼ�Ƿ���Ӧ����¼�  .
         */
        mMapController.enableClick(true);
        /**
         * ���õ�ͼ���ż���
         */
        mMapController.setZoom(MAP_ZOOM);
        /**
         * ��ʾ�������ſؼ�
         */
        mMapView.setBuiltInZoomControls(true);
       
        /**
         * ����ͼ�ƶ����찲��
         * ʹ�ðٶȾ�γ�����꣬����ͨ��http://api.map.baidu.com/lbsapi/getpoint/index.html��ѯ��������
         * �����Ҫ�ڰٶȵ�ͼ����ʾʹ����������ϵͳ��λ�ã��뷢�ʼ���mapapi@baidu.com��������ת���ӿ�
         */
        //double cLat = 39.945 ;
        //double cLon = 116.404 ;
        GeoPoint p = new GeoPoint((int)(default_x * 1E6), (int)(default_y * 1E6));
        mMapController.setCenter(p);
         
        showMylocation();
	}
	
	public void showMylocation()
	{
		myLocationOverlay = new MyLocationOverlay(mMapView);  
		LocationData locData = new LocationData();  
		//�ֶ���λ��Դ��Ϊ�찲�ţ���ʵ��Ӧ���У���ʹ�ðٶȶ�λSDK��ȡλ����Ϣ��Ҫ��SDK����ʾһ��λ�ã���Ҫʹ�ðٶȾ�γ�����꣨bd09ll��  
		locData.latitude = default_x;  
		locData.longitude = default_y;  
		locData.direction = 2.0f;
		locData.accuracy = 2000;
		myLocationOverlay.setData(locData);  
		mMapView.getOverlays().add(myLocationOverlay);  
		mMapView.refresh();  
		mMapView.getController().animateTo(newGeoPoint((int)(locData.latitude*1e6),  
		(int)(locData.longitude* 1e6)));  
	}
	
	
	public void getLastLocation()
	{
		String str_x = sharedPrefs.getString("default_x", "39.945");
		String str_y = sharedPrefs.getString("default_y", "116.404");
		
		default_x = Double.parseDouble(str_x);
		default_y = Double.parseDouble(str_y);
	}
	
	
	public void setLastLocation(Location loc)
	{
		String str_x = String.valueOf(loc.getLatitude());
		String str_y = String.valueOf(loc.getLongitude());
		
		Editor editor = sharedPrefs.edit();
		editor.putString("default_x", str_x);
		editor.putString("default_y", str_y);
		editor.commit();
	}
	
	
	public void updateLocation(Location location)
	{
        if (location == null)
            return ;
        

        		
        
        //save the loc
        setLastLocation(location);
        
        LocationData locData = new LocationData();  
        locData.latitude = location.getLatitude();
        locData.longitude = location.getLongitude();
        //�������ʾ��λ����Ȧ����accuracy��ֵΪ0����
        locData.accuracy = location.getAccuracy();
       // locData.direction = location.ge
        //���¶�λ����
        myLocationOverlay.setData(locData);
        //����ͼ������ִ��ˢ�º���Ч
        if(mMapView == null)
        	return;
        
        mMapView.refresh();
        //���ֶ�����������״ζ�λʱ���ƶ�����λ��
        if (isFirstLoc){
        	//�ƶ���ͼ����λ��
            mMapController.animateTo(new GeoPoint((int)(locData.latitude* 1e6), (int)(locData.longitude *  1e6)));
            isRequest = false;
        }
        //mMapController.setZoom(5);
        //�״ζ�λ���
        isFirstLoc = false;		
		
	}
	
	
	class IncomingHandler extends Handler {
	    @Override
	    public void handleMessage(Message msg) {
	        switch (msg.what) {
	            case 0x01:
	                Location loc = (Location)msg.obj;
	                convertLocation(loc);
	                Log.v("Localworld","Rev Msg=" + loc.toString());
	                break;
	            default:
	                super.handleMessage(msg);
	        }
	    }
	}
	
	final Messenger mMessenger = new Messenger(new IncomingHandler());
	
	
	
	private GeoPoint newGeoPoint(int i, int j) {
		// TODO Auto-generated method stub
		return null;
	}


	@Override
	protected void onStop() {
		super.onStop();
		Log.d("Localworld", "Stop the app...");
		

	}
	
    @Override
    protected void onPause() {
    	/**
    	 *  MapView������������Activityͬ������activity����ʱ�����MapView.onPause()
    	 */
        mMapView.onPause();
        super.onPause();
    }
    
    @Override
    protected void onResume() {
    	/**
    	 *  MapView������������Activityͬ������activity�ָ�ʱ�����MapView.onResume()
    	 */
        mMapView.onResume();
        super.onResume();
    }
    
    
	@Override
	protected void onDestroy () {	
		mMapView.destroy();
		s.clearMessagerHandler();
		unbindService(mConnection);
		
		Log.d("Localworld", "Destroy the app...");	
		super.onDestroy();
	}
	
	  @Override
	    protected void onSaveInstanceState(Bundle outState) {
	    	super.onSaveInstanceState(outState);
	    	mMapView.onSaveInstanceState(outState);
	    	
	    }
	    
	    @Override
	    protected void onRestoreInstanceState(Bundle savedInstanceState) {
	    	super.onRestoreInstanceState(savedInstanceState);
	    	mMapView.onRestoreInstanceState(savedInstanceState);
	    }
	
	

	private ServiceConnection mConnection = new ServiceConnection() {

		public void onServiceConnected(ComponentName className, IBinder binder) {
			s = ((LocalWordService.MyBinder) binder).getService();
			Toast.makeText(MainActivity.this, "Connected", Toast.LENGTH_SHORT)
					.show();
			s.setMessagerHandler(mMessenger);
		}

		public void onServiceDisconnected(ComponentName className) {
			s = null;
			s.clearMessagerHandler();
		}
		
		
	};
	
	
	void doBindService() {
		bindService(new Intent(this, LocalWordService.class), mConnection,
				Context.BIND_AUTO_CREATE);
	}

	
	
	
	public void showServiceData(View view) {

		if(mMapView != null)
		{
			if(isMapView)
			{
				isMapView = false;
				mMapView.setSatellite(false);
			}
			else
			{
				isMapView = true;
				mMapView.setSatellite(true);
			}
		}
		
	}
	
	
	public void stopMyService(View view)
	{
		AlarmManager alarmManager = (AlarmManager) this
				.getSystemService(Context.ALARM_SERVICE);
		Intent i = new Intent(this, MyStartServiceReceiver.class);
		PendingIntent pending = PendingIntent.getBroadcast(this, 0, i,
				PendingIntent.FLAG_CANCEL_CURRENT);
		alarmManager.cancel(pending);
		Toast.makeText(this, "Stop the service",
				Toast.LENGTH_SHORT).show();
		
	}
	
	public void startMyService(View view)
	{
		AlarmManager service = (AlarmManager) this
				.getSystemService(Context.ALARM_SERVICE);
		Intent i = new Intent(this, MyStartServiceReceiver.class);
		PendingIntent pending = PendingIntent.getBroadcast(this, 0, i,
				PendingIntent.FLAG_CANCEL_CURRENT);
		Calendar cal = Calendar.getInstance();
		// Start 30 seconds after boot completed
		cal.add(Calendar.SECOND, 30);
		//
		// Fetch every 30 seconds
		// InexactRepeating allows Android to optimize the energy consumption
		service.setInexactRepeating(AlarmManager.RTC_WAKEUP,
				cal.getTimeInMillis(), 1000 * 30, pending);
	
		Toast.makeText(this, "Start the service",
				Toast.LENGTH_SHORT).show();
	}
	
	
	public void startLocationService(View view)
	{
				
		if(sharedPrefs.getBoolean("isServiceStart", true))
		{
			Log.d("Localworld", "Stop the location service");
			stopMyService(view);
			Editor editor = sharedPrefs.edit();
			editor.putBoolean("isServiceStart", false);
			editor.commit();
			startbutton.setText(R.string.start_name);
			Toast.makeText(this, "Stop the service...",
					Toast.LENGTH_LONG).show();
		}
		else
		{	
			Log.d("Localworld", "Start the location service");
			startMyService(view);
			Editor editor = sharedPrefs.edit();
			editor.putBoolean("isServiceStart", true);
			editor.commit();
			startbutton.setText(R.string.stop_name);
			Toast.makeText(this, "Start the service...",
					Toast.LENGTH_LONG).show();
		}
		
	}
	
	public boolean isStartLocationService()
	{
		if(sharedPrefs.getBoolean("isServiceStart", true))
			return true;
		else
			return false;
	}


	public void startSetting(View view)
	{
		Toast.makeText(this, "Settings������",
				Toast.LENGTH_SHORT).show();
	}
	
	
	//convert the location to baidu GPS location
	
	
	public void convertLocation(Location loc)
	{
		//http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x=116.254615&y=29.814476
		
		if(isConvertTaskRun)
			return;
		conv_loc = loc;
		isConvertTaskRun = true;
		StringBuffer baidu_url = new StringBuffer(128);
		baidu_url.append(BAIDU_URL);
		baidu_url.append("x=" + loc.getLongitude());
		baidu_url.append("&y=" + loc.getLatitude());
		mytask = new doConvertTask();
		mytask.execute(baidu_url.toString());
		//new doConvertTask().execute(baidu_url.toString());
	}
	
	private class doConvertTask extends AsyncTask<String, String, String> {
	     protected String doInBackground(String...url ) {
	         HttpClient httpclient = new DefaultHttpClient();
	         HttpResponse response;
	         String responseString = null;
	         try {
	             response = httpclient.execute(new HttpGet(url[0]));
	             StatusLine statusLine = response.getStatusLine();
	             if(statusLine.getStatusCode() == HttpStatus.SC_OK){
	                 ByteArrayOutputStream out = new ByteArrayOutputStream();
	                 response.getEntity().writeTo(out);
	                 out.close();
	                 responseString = out.toString();
	                 
	             } 
	             else
	             {
	                 //Closes the connection.
	                 response.getEntity().getContent().close();
	                 throw new IOException(statusLine.getReasonPhrase());
	             }
	         } catch (ClientProtocolException e) {
	        	 e.printStackTrace();
	         } catch (IOException e) {
	        	 e.printStackTrace();
	         }
	         
	         return responseString;
	     }



	     protected void onPostExecute(String result) 
	     {
	    	 Log.v("Local world","GetFromBaidu:" + result);
	    	 
	    	 //{"error":0,"x":"MTE2LjI2MTA5OTEyMjE=","y":"MjkuODIwNTYwODc0ODQ2"}
	    	 if(result == null)
	    	 {
	    		 isConvertTaskRun = false;
	    		 return;
	    	 }
	    	 
			try {
				JSONObject mainObject = new JSONObject(result);
				int ret = mainObject.getInt("error");
				if(ret == 0)  // ok
				{
					String x = mainObject.getString("x");
					String y = mainObject.getString("y");
					byte[] byte_x =  Base64.decode(x, Base64.DEFAULT);
					byte[] byte_y =  Base64.decode(y, Base64.DEFAULT);


					String textx = new String(byte_x, "UTF-8");
					String texty = new String(byte_y, "UTF-8");
					double loc_x = Double.parseDouble(textx);
					double loc_y = Double.parseDouble(texty);
					
					conv_loc.setLongitude(loc_x);
					conv_loc.setLatitude(loc_y);
					updateLocation(conv_loc);
					//Log.v("Local world", "LOC=" + conv_loc.toString());
					
				}
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (UnsupportedEncodingException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

			isConvertTaskRun = false;
	     }
	 }
	
}