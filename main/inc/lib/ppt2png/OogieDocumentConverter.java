//
// JODConverter - Java OpenDocument Converter
// Copyright (C) 2004-2007 - Mirko Nasato <mirko@artofsolving.com>
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
// http://www.gnu.org/copyleft/lesser.html
//

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.HashMap;
import java.util.Map;

import org.apache.commons.io.FilenameUtils;
import org.apache.commons.io.IOUtils;

import com.artofsolving.jodconverter.DocumentConverter;
import com.artofsolving.jodconverter.DocumentFormat;
import com.artofsolving.jodconverter.DocumentFormatRegistry;
import com.artofsolving.jodconverter.openoffice.connection.OpenOfficeConnection;
import com.artofsolving.jodconverter.openoffice.connection.OpenOfficeException;
import com.artofsolving.jodconverter.openoffice.converter.StreamOpenOfficeDocumentConverter;
import com.sun.star.beans.PropertyValue;
import com.sun.star.container.XNamed;
import com.sun.star.document.XExporter;
import com.sun.star.document.XFilter;
import com.sun.star.drawing.XDrawPage;
import com.sun.star.drawing.XDrawPages;
import com.sun.star.drawing.XDrawPagesSupplier;
import com.sun.star.frame.XComponentLoader;
import com.sun.star.lang.XComponent;
import com.sun.star.lang.XMultiComponentFactory;
import com.sun.star.ucb.XFileIdentifierConverter;
import com.sun.star.uno.UnoRuntime;

/**
 * Default file-based {@link DocumentConverter} implementation.
 * <p>
 * This implementation passes document data to and from the OpenOffice.org
 * service as file URLs.
 * <p>
 * File-based conversions are faster than stream-based ones (provided by
 * {@link StreamOpenOfficeDocumentConverter}) but they require the
 * OpenOffice.org service to be running locally and have the correct
 * permissions to the files.
 * 
 * @see StreamOpenOfficeDocumentConverter
 */
public class OogieDocumentConverter extends AbstractDokeosDocumentConverter {
	
	public OogieDocumentConverter(OpenOfficeConnection connection, int width, int height) {		
		super(connection, width, height);
	}

	public OogieDocumentConverter(OpenOfficeConnection connection, DocumentFormatRegistry formatRegistry, int width, int height) {
		super(connection, formatRegistry, width, height);
	}

	protected void loadAndExport(String inputUrl, Map/*<String,Object>*/ loadProperties, String outputUrl, Map/*<String,Object>*/ storeProperties) throws Exception {
		XComponentLoader desktop = openOfficeConnection.getDesktop();
		XComponent document = desktop.loadComponentFromURL(inputUrl, "_blank", 0, toPropertyValues(loadProperties));
        if (document == null) {
            throw new OpenOfficeException("conversion failed: input document is null after loading");
        }
		
		refreshDocument(document);
		
		try {
			
			outputUrl = FilenameUtils.getFullPath(outputUrl)+FilenameUtils.getBaseName(outputUrl);
			
//			 filter
			PropertyValue[] loadProps = new PropertyValue[4];
			
			// type of image
			loadProps[0] = new PropertyValue();
			loadProps[0].Name = "MediaType";
			loadProps[0].Value = "image/png";
			
			// Height and width
			PropertyValue[] filterDatas = new PropertyValue[4];
			for(int i = 0; i<4 ; i++){
				filterDatas[i] = new PropertyValue();
			}
			
			filterDatas[0].Name = "PixelWidth";
			filterDatas[0].Value = new Integer(this.width);
			filterDatas[1].Name = "PixelHeight";
			filterDatas[1].Value = new Integer(this.height);
			filterDatas[2].Name = "LogicalWidth";
			filterDatas[2].Value = new Integer(2000);
			filterDatas[3].Name = "LogicalHeight";
			filterDatas[3].Value = new Integer(2000);
			
			
			XDrawPagesSupplier pagesSupplier = (XDrawPagesSupplier) UnoRuntime
			.queryInterface(XDrawPagesSupplier.class, document);
			//System.out.println(pagesSupplier.toString());				
			XDrawPages pages = pagesSupplier.getDrawPages();
			int nbPages = pages.getCount();
			
			
			for (int i = 0; i < nbPages; i++) {
				
				XDrawPage page = (XDrawPage) UnoRuntime.queryInterface(
						com.sun.star.drawing.XDrawPage.class, pages
								.getByIndex(i));
				
				XNamed xPageName = (XNamed)UnoRuntime.queryInterface(XNamed.class,page);
				
				xPageName.setName("slide"+(i+1));
				if(!xPageName.getName().equals("slide"+(i+1)) && !xPageName.getName().equals("page"+(i+1)))
					xPageName.setName((i+1)+"-"+xPageName.getName());
				
				
	            XMultiComponentFactory localServiceManager = ((DokeosSocketOfficeConnection)this.openOfficeConnection).getServiceManager();
				Object GraphicExportFilter = localServiceManager
						.createInstanceWithContext(
								"com.sun.star.drawing.GraphicExportFilter",
								((DokeosSocketOfficeConnection)this.openOfficeConnection).getComponentContext());
				
				XExporter xExporter = (XExporter) UnoRuntime
						.queryInterface(XExporter.class,
								GraphicExportFilter);

				XComponent xComp = (XComponent) UnoRuntime
						.queryInterface(XComponent.class, page);

				xExporter.setSourceDocument(xComp);
				loadProps[1] = new PropertyValue();
				loadProps[1].Name = "URL";
				
				
				
				loadProps[1].Value = outputUrl+"/"+xPageName.getName()+".png";
				loadProps[2] = new PropertyValue();
				loadProps[2].Name = "FilterData";
				loadProps[2].Value = filterDatas;
				loadProps[3] = new PropertyValue(); 
				loadProps[3].Name = "Quality"; 
				loadProps[3].Value = new Integer(100);
				
				XFilter xFilter = (XFilter) UnoRuntime.queryInterface(XFilter.class, GraphicExportFilter);

				xFilter.filter(loadProps);
				System.out.println(xPageName.getName()+".png");
				
			}
			
		} finally {
			document.dispose();
		}
	}
}
