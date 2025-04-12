
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/components/ui/use-toast";

const Index = () => {
  const { toast } = useToast();

  const showToast = () => {
    toast({
      title: "Success!",
      description: "The Lovable template has been updated successfully.",
    });
  };

  return (
    <div className="min-h-screen p-8 bg-gray-50 flex items-center justify-center">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle className="text-2xl font-bold">Welcome to Lovable</CardTitle>
          <CardDescription>The template has been updated to the latest version</CardDescription>
        </CardHeader>
        <CardContent>
          <p className="text-gray-600 mb-4">
            This project template now includes the latest Lovable features and improvements.
            Click the button below to confirm everything is working.
          </p>
        </CardContent>
        <CardFooter>
          <Button onClick={showToast} className="w-full">Test Toast Notification</Button>
        </CardFooter>
      </Card>
    </div>
  );
};

export default Index;
