import org.eclipse.birt.report.engine.api.HTMLImageHandler;
import org.eclipse.birt.report.engine.api.script.IReportContext;

import org.eclipse.birt.report.engine.api.IImage;
import org.eclipse.birt.report.engine.api.HTMLRenderContext;
import java.util.Map;
import org.eclipse.birt.report.engine.api.EngineConstants;
import org.eclipse.birt.report.engine.api.CachedImage;

/**
 * Parche para BIRT para que reutilice las imagenes y las saque de /resource
 * @author Martin A. Santangelo
 */
public class RadBirtHtmlImageHandler extends org.eclipse.birt.report.engine.api.HTMLImageHandler
{
    // public String onFileImage( IImage image, Object context )
    // {
		// return onFileImage( image, context); //$NON-NLS-1$
    // }


	public String onFileImage( IImage image, Object context )
    {
        HTMLRenderContext myContext = (HTMLRenderContext) context;
		String imageURL = myContext.getBaseImageURL( );
		String imageDir = myContext.getImageDirectory( );

		int pos = image.getID().indexOf("/resources/");
		
		return image.getID().substring(pos);
    }
}